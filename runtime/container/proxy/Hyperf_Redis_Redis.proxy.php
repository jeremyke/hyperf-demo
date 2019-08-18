<?php

declare (strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */
namespace Hyperf\Redis;

use Hyperf\Redis\Pool\PoolFactory;
use Hyperf\Utils\Context;
class Redis_0eb4423c5374596c9bd3d455437920ab extends Redis
{
    use \Hyperf\Di\Aop\ProxyTrait;
    /**
     * @var PoolFactory
     */
    protected $factory;
    /**
     * @var string
     */
    protected $poolName = 'default';
    public function __construct(PoolFactory $factory)
    {
        $this->factory = $factory;
    }
    public function __call($name, $arguments)
    {
        $__function__ = __FUNCTION__;
        $__method__ = __METHOD__;
        return self::__proxyCall(Redis::class, __FUNCTION__, self::getParamsMap(Redis::class, __FUNCTION__, func_get_args()), function ($name, $arguments) use($__function__, $__method__) {
            // Get a connection from coroutine context or connection pool.
            $hasContextConnection = Context::has($this->getContextKey());
            $connection = $this->getConnection($hasContextConnection);
            try {
                // Execute the command with the arguments.
                $result = $connection->{$name}(...$arguments);
            } finally {
                // Release connection.
                if (!$hasContextConnection) {
                    if ($this->shouldUseSameConnection($name)) {
                        if ($name === 'select' && ($db = $arguments[0])) {
                            $connection->setDatabase((int) $db);
                        }
                        // Should storage the connection to coroutine context, then use defer() to release the connection.
                        Context::set($this->getContextKey(), $connection);
                        defer(function () use($connection) {
                            $connection->release();
                        });
                    } else {
                        // Release the connection after command executed.
                        $connection->release();
                    }
                }
            }
            return $result;
        });
    }
    /**
     * Define the commands that needs same connection to execute.
     * When these commands executed, the connection will storage to coroutine context.
     */
    private function shouldUseSameConnection(string $methodName) : bool
    {
        return in_array($methodName, ['multi', 'pipeline', 'select']);
    }
    /**
     * Get a connection from coroutine context, or from redis connectio pool.
     * @param mixed $hasContextConnection
     */
    private function getConnection($hasContextConnection) : RedisConnection
    {
        $connection = null;
        if ($hasContextConnection) {
            $connection = Context::get($this->getContextKey());
        }
        if (!$connection instanceof RedisConnection) {
            $pool = $this->factory->getPool($this->poolName);
            $connection = $pool->get()->getConnection();
        }
        return $connection;
    }
    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey() : string
    {
        return sprintf('redis.connection.%s', $this->poolName);
    }
}