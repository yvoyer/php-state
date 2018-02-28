<?php

namespace Star\Component\State;

use Star\Component\State\Event\StateEvent;

interface EventRegistry
{
    /**
     * @param string $name
     * @param StateEvent $event
     */
    public function dispatch($name, StateEvent $event);

    /**
     * @param string $event
     * @param callable $listener
     */
    public function addListener($event, $listener);
}
