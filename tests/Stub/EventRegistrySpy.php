<?php declare(strict_types=1);

namespace Star\Component\State\Stub;

use Star\Component\State\Event\StateEvent;
use Star\Component\State\EventRegistry;

final class EventRegistrySpy implements EventRegistry
{
    /**
     * @var array<string, callable[]>
     */
    private array $listeners = [];

    /**
     * @var array<string, array<int, StateEvent>>
     */
    private array $dispatches = [];

    public function dispatch(string $name, StateEvent $event): void
    {
        $this->dispatches[$name][] = $event;
    }

    public function addListener(string $event, callable $listener): void
    {
        $this->listeners[$event][] = $listener;
    }

    /**
     * @return StateEvent[]
     */
    public function getDispatchedEvents(string $event): array
    {
        return $this->dispatches[$event] ?? [];
    }

    /**
     * @return callable[]
     */
    public function getListenersOfEvent(string $event): array
    {
        return $this->listeners[$event] ?? [];
    }
}
