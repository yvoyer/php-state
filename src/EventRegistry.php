<?php declare(strict_types=1);

namespace Star\Component\State;

use Star\Component\State\Event\StateEvent;

interface EventRegistry
{
    public function dispatch(string $name, StateEvent $event): void;

    public function addListener(string $event, callable $listener): void;
}
