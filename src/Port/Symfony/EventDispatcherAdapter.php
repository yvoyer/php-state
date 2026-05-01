<?php declare(strict_types=1);

namespace Star\Component\State\Port\Symfony;

use Star\Component\State\Event\StateEvent;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\EventRegistry;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EventDispatcherAdapter implements EventRegistry
{
    private EventDispatcherInterface $dispatcher;

    public function __construct(
        ?EventDispatcherInterface $dispatcher = null
    ) {
        if (!$dispatcher) {
            $dispatcher = new EventDispatcher();
        }
        $this->dispatcher = $dispatcher;
    }

    public function dispatch(string $name, StateEvent $event): void
    {
        $this->dispatcher->dispatch($event, StateEventStore::eventNameFromClass($event));
    }

    public function addListener(string $event, callable $listener): void
    {
        $this->dispatcher->addListener($event, $listener);
    }
}
