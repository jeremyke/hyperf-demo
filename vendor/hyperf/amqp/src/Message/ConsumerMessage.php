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

namespace Hyperf\Amqp\Message;

use Hyperf\Amqp\Builder\QueueBuilder;
use Hyperf\Amqp\Packer\Packer;
use Hyperf\Utils\ApplicationContext;
use Psr\Container\ContainerInterface;

abstract class ConsumerMessage extends Message implements ConsumerMessageInterface
{
    /**
     * @var ContainerInterface
     */
    public $container;

    /**
     * @var string
     */
    protected $queue;

    /**
     * @var bool
     */
    protected $requeue = true;

    /**
     * @var array
     */
    protected $routingKey = [];

    public function setQueue(string $queue): self
    {
        $this->queue = $queue;
        return $this;
    }

    public function getQueue(): string
    {
        return $this->queue;
    }

    public function isRequeue(): bool
    {
        return $this->requeue;
    }

    public function getQueueBuilder(): QueueBuilder
    {
        return (new QueueBuilder())->setQueue($this->getQueue());
    }

    public function unserialize(string $data)
    {
        $container = ApplicationContext::getContainer();
        $packer = $container->get(Packer::class);

        return $packer->unpack($data);
    }

    public function getConsumerTag(): string
    {
        return implode(',', (array) $this->getRoutingKey());
    }
}
