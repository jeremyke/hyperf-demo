<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace Hyperf\AsyncQueue\Driver;

use Hyperf\AsyncQueue\Exception\InvalidQueueException;
use Hyperf\AsyncQueue\JobInterface;
use Hyperf\AsyncQueue\Message;
use Hyperf\AsyncQueue\MessageInterface;
use Psr\Container\ContainerInterface;
use Redis;

class RedisDriver extends Driver
{
    /**
     * @var \Redis
     */
    protected $redis;

    /**
     * @var ChannelConfig
     */
    protected $channel;

    /**
     * Max polling time.
     * @var int
     */
    protected $timeout;

    /**
     * Retry delay time.
     * @var int
     */
    protected $retrySeconds;

    /**
     * Handle timeout.
     * @var int
     */
    protected $handleTimeout;

    public function __construct(ContainerInterface $container, $config)
    {
        parent::__construct($container, $config);
        $channel = $config['channel'] ?? 'queue';

        $this->redis = $container->get(Redis::class);
        $this->timeout = $config['timeout'] ?? 5;
        $this->retrySeconds = $config['retry_seconds'] ?? 10;
        $this->handleTimeout = $config['handle_timeout'] ?? 10;

        $this->channel = make(ChannelConfig::class, ['channel' => $channel]);
    }

    public function push(JobInterface $job, int $delay = 0): bool
    {
        $message = new Message($job);
        $data = $this->packer->pack($message);

        if ($delay === 0) {
            return (bool) $this->redis->lPush($this->channel->getWaiting(), $data);
        }

        return $this->redis->zAdd($this->channel->getDelayed(), time() + $delay, $data) > 0;
    }

    /**
     * @deprecated v1.1
     */
    public function delay(JobInterface $job, int $delay = 0): bool
    {
        if ($delay === 0) {
            return $this->push($job);
        }

        $message = new Message($job);
        $data = $this->packer->pack($message);
        return $this->redis->zAdd($this->channel->getDelayed(), time() + $delay, $data) > 0;
    }

    public function delete(JobInterface $job): bool
    {
        $message = new Message($job);
        $data = $this->packer->pack($message);

        return (bool) $this->redis->zRem($this->channel->getDelayed(), $data);
    }

    public function pop(): array
    {
        $this->move($this->channel->getDelayed(), $this->channel->getWaiting());
        $this->move($this->channel->getReserved(), $this->channel->getTimeout());

        $res = $this->redis->brPop($this->channel->getWaiting(), $this->timeout);
        if (! isset($res[1])) {
            return [false, null];
        }

        $data = $res[1];
        $message = $this->packer->unpack($data);
        if (! $message) {
            return [false, null];
        }

        $this->redis->zadd($this->channel->getReserved(), time() + $this->handleTimeout, $data);

        return [$data, $message];
    }

    public function ack($data): bool
    {
        return $this->remove($data);
    }

    public function fail($data): bool
    {
        if ($this->remove($data)) {
            return (bool) $this->redis->lPush($this->channel->getFailed(), $data);
        }
        return false;
    }

    public function reload(string $queue = null): int
    {
        $channel = $this->channel->getFailed();
        if ($queue) {
            if (! in_array($queue, ['timeout', 'failed'])) {
                throw new InvalidQueueException(sprintf('Queue %s is not supported.', $queue));
            }

            $channel = $this->channel->get($queue);
        }

        $num = 0;
        while ($this->redis->rpoplpush($channel, $this->channel->getWaiting())) {
            ++$num;
        }
        return $num;
    }

    public function flush(string $queue = null): bool
    {
        $channel = $this->channel->getFailed();
        if ($queue) {
            $channel = $this->channel->get($queue);
        }

        return (bool) $this->redis->del($channel);
    }

    public function info(): array
    {
        return [
            'waiting' => $this->redis->lLen($this->channel->getWaiting()),
            'delayed' => $this->redis->zCard($this->channel->getDelayed()),
            'failed' => $this->redis->lLen($this->channel->getFailed()),
            'timeout' => $this->redis->lLen($this->channel->getTimeout()),
        ];
    }

    protected function retry(MessageInterface $message): bool
    {
        $data = $this->packer->pack($message);
        return $this->redis->zAdd($this->channel->getDelayed(), time() + $this->retrySeconds, $data) > 0;
    }

    /**
     * Remove data from reserved queue.
     * @param mixed $data
     */
    protected function remove($data): bool
    {
        return $this->redis->zrem($this->channel->getReserved(), $data) > 0;
    }

    /**
     * Move message to the waiting queue.
     */
    protected function move(string $from, string $to): void
    {
        $now = time();
        $options = ['LIMIT' => [0, 100]];
        if ($expired = $this->redis->zrevrangebyscore($from, (string) $now, '-inf', $options)) {
            foreach ($expired as $job) {
                if ($this->redis->zRem($from, $job) > 0) {
                    $this->redis->lPush($to, $job);
                }
            }
        }
    }
}
