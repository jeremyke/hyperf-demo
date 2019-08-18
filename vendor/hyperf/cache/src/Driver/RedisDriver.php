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

namespace Hyperf\Cache\Driver;

use Hyperf\Cache\Exception\InvalidArgumentException;
use Psr\Container\ContainerInterface;

class RedisDriver extends Driver implements KeyCollectorInterface
{
    /**
     * @var \Redis
     */
    protected $redis;

    public function __construct(ContainerInterface $container, array $config)
    {
        parent::__construct($container, $config);

        $this->redis = $container->get(\Redis::class);
    }

    public function get($key, $default = null)
    {
        $res = $this->redis->get($this->getCacheKey($key));
        if ($res === false) {
            return $default;
        }

        return $this->packer->unpack($res);
    }

    public function fetch(string $key, $default = null): array
    {
        $res = $this->redis->get($this->getCacheKey($key));
        if ($res === false) {
            return [false, $default];
        }

        return [true, $this->packer->unpack($res)];
    }

    public function set($key, $value, $ttl = null)
    {
        $res = $this->packer->pack($value);
        if ($ttl > 0) {
            return $this->redis->set($this->getCacheKey($key), $res, $ttl);
        }

        return $this->redis->set($this->getCacheKey($key), $res);
    }

    public function delete($key)
    {
        return (bool) $this->redis->del($this->getCacheKey($key));
    }

    public function clear()
    {
        return $this->clearPrefix('');
    }

    public function getMultiple($keys, $default = null)
    {
        $cacheKeys = array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);

        $values = $this->redis->mget($cacheKeys);
        $result = [];
        foreach ($keys as $i => $key) {
            $result[$key] = $values[$i] === false ? $default : $this->packer->unpack($values[$i]);
        }

        return $result;
    }

    public function setMultiple($values, $ttl = null)
    {
        if (! is_array($values)) {
            throw new InvalidArgumentException('The values is invalid!');
        }

        $cacheKeys = [];
        foreach ($values as $key => $value) {
            $cacheKeys[$this->getCacheKey($key)] = $this->packer->pack($value);
        }

        $ttl = (int) $ttl;
        if ($ttl > 0) {
            foreach ($cacheKeys as $key => $value) {
                $this->redis->set($key, $value, $ttl);
            }

            return true;
        }

        return $this->redis->mset($cacheKeys);
    }

    public function deleteMultiple($keys)
    {
        $cacheKeys = array_map(function ($key) {
            return $this->getCacheKey($key);
        }, $keys);

        return $this->redis->del(...$cacheKeys);
    }

    public function has($key)
    {
        return (bool) $this->redis->exists($this->getCacheKey($key));
    }

    public function clearPrefix(string $prefix): bool
    {
        $iterator = null;
        $key = $prefix . '*';
        while ($keys = $this->redis->scan($iterator, $this->getCacheKey($key), 10000)) {
            $this->redis->del(...$keys);
        }

        return true;
    }

    public function addKey(string $collector, string $key): bool
    {
        return (bool) $this->redis->sAdd($this->getCacheKey($collector), $key);
    }

    public function keys(string $collector): array
    {
        return $this->redis->sMembers($this->getCacheKey($collector)) ?? [];
    }

    public function delKey(string $collector, ...$key): bool
    {
        return (bool) $this->redis->sRem($this->getCacheKey($collector), ...$key);
    }
}
