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

namespace Hyperf\Tracer\Aspect;

use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Aop\AroundInterface;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Redis\Redis;
use Hyperf\Tracer\SwitchManager;
use Hyperf\Tracer\Tracing;

/**
 * @Aspect
 */
class RedisAspect implements AroundInterface
{
    /**
     * @var array
     */
    public $classes
        = [
            Redis::class . '::__call',
        ];

    /**
     * @var array
     */
    public $annotations = [];

    /**
     * @var Tracing
     */
    private $tracing;

    /**
     * @var SwitchManager
     */
    private $switchManager;

    public function __construct(Tracing $tracing, SwitchManager $switchManager)
    {
        $this->tracing = $tracing;
        $this->switchManager = $switchManager;
    }

    /**
     * @return mixed return the value from process method of ProceedingJoinPoint, or the value that you handled
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        if ($this->switchManager->isEnable('redis') === false) {
            return $proceedingJoinPoint->process();
        }

        $arguments = $proceedingJoinPoint->arguments['keys'];
        $span = $this->tracing->span('Redis' . '::' . $arguments['name']);
        $span->start();
        $span->tag('arguments', json_encode($arguments['arguments']));
        $result = $proceedingJoinPoint->process();
        $span->tag('result', json_encode($result));
        $span->finish();
        return $result;
    }
}
