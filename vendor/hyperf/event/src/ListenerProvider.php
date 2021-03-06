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

namespace Hyperf\Event;

use Psr\EventDispatcher\ListenerProviderInterface;
use SplPriorityQueue;

class ListenerProvider implements ListenerProviderInterface
{
    /**
     * @var callable[]
     */
    public $listeners = [];

    /**
     * @param object $event An event for which to return the relevant listeners
     * @return iterable[callable] An iterable (array, iterator, or generator) of callables.  Each
     *                            callable MUST be type-compatible with $event.
     */
    public function getListenersForEvent($event): iterable
    {
        $queue = new SplPriorityQueue();
        foreach ($this->listeners as $listener) {
            if ($event instanceof $listener->event) {
                $queue->insert($listener->listener, $listener->priority);
            }
        }
        return $queue;
    }

    public function on(string $event, callable $listener, int $priority = 1): void
    {
        $this->listeners[] = new ListenerData($event, $listener, $priority);
    }
}
