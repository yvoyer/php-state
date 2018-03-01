<?php

namespace Star\Component\State\Port\Symfony;

use Star\Component\State\Event\StateEvent;
use Star\Component\State\EventRegistry;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherAdapter implements EventRegistry
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher = null)
    {
        if (! $dispatcher) {
            $dispatcher = new EventDispatcher();
        }
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param string $name
     * @param StateEvent $event
     */
    public function dispatch($name, StateEvent $event)
    {
        $this->dispatcher->dispatch($name, $event);
    }

    /**
     * @param string $event
     * @param callable $listener
     */
    public function addListener($event, $listener)
    {
        $this->dispatcher->addListener($event, $listener);
    }
}
