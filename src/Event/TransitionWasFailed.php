<?php declare(strict_types=1);

namespace Star\Component\State\Event;

use Star\Component\State\StateContext;
use Symfony\Contracts\EventDispatcher\Event;
use Throwable;

final class TransitionWasFailed extends Event implements StateEvent
{
    private string $transition;
    private string $previousState;
    private string $destinationState;
    private StateContext $context;
    private Throwable $exception;

    public function __construct(
        string $transition,
        string $previousState,
        string $destinationState,
        StateContext $context,
        Throwable $exception
    ) {
        $this->transition = $transition;
        $this->previousState = $previousState;
        $this->destinationState = $destinationState;
        $this->context = $context;
        $this->exception = $exception;
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
