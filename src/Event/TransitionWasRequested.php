<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Event;

use Star\Component\State\StateContext;
use Symfony\Contracts\EventDispatcher\Event;

final class TransitionWasRequested extends Event implements StateEvent
{
    private string $transition;
    private string $previousState;
    private string $destinationState;
    private StateContext $context;

    public function __construct(
        string $transition,
        string $previousState,
        string $destinationState,
        StateContext $context
    ) {
        $this->transition = $transition;
        $this->previousState = $previousState;
        $this->destinationState = $destinationState;
        $this->context = $context;
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
}
