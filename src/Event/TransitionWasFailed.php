<?php declare(strict_types=1);

namespace Star\Component\State\Event;

use Star\Component\State\StateContext;
use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class TransitionWasFailed extends Event implements StateEvent
{
    public function __construct(
        private readonly string $transition,
        private readonly string $previousState,
        private readonly string $destinationState,
        private readonly StateContext $context,
        private readonly Throwable $exception,
    ) {
    }

    public function transition(): string
    {
        return $this->transition;
    }

    public function getPreviousState(): string
    {
        return $this->previousState;
    }

    public function getDestinationState(): string
    {
        return $this->destinationState;
    }

    public function getContext(): StateContext
    {
        return $this->context;
    }

    public function exception(): \Throwable
    {
        return $this->exception;
    }
}
