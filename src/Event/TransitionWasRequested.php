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
    public function __construct(
        private readonly string $transition,
        private readonly string $previousState,
        private readonly string $destinationState,
        private readonly StateContext $context,
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
}
