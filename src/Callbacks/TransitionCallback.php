<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

interface TransitionCallback
{
    /**
     * @see StateContext
     */
    public function beforeStateChange(
        StateContext $context,
        StateMachine $machine,
    ): void;

    /**
     * @see StateContext
     */
    public function afterStateChange(
        StateContext $context,
        StateMachine $machine,
    ): void;

    /**
     * @return string The new state to move to on failure
     * @see StateContext
     */
    public function onFailure(
        InvalidStateTransitionException $exception,
        StateContext $context,
        StateMachine $machine,
    ): string;
}
