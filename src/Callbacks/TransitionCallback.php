<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

interface TransitionCallback
{
    /**
     * @param mixed|StateContext $context
     * @param StateMachine $machine
     * @deprecated $context will expect a type of StateContext in 4.0, you need to update your implementations.
     * @see StateContext
     */
    public function beforeStateChange(
        /* StateContext in 4.0 */ $context,
        StateMachine $machine,
    ): void;

    /**
     * @param mixed|StateContext $context
     * @param StateMachine $machine
     * @deprecated $context will expect a type of StateContext in 4.0, you need to update your implementations.
     * @see StateContext
     */
    public function afterStateChange(
        /* StateContext in 4.0 */ $context,
        StateMachine $machine,
    ): void;

    /**
     * @param InvalidStateTransitionException $exception
     * @param mixed|StateContext $context
     * @param StateMachine $machine
     *
     * @return string The new state to move to on failure
     * @deprecated $context will expect a type of StateContext in 4.0, you need to update your implementations.
     * @see StateContext
     */
    public function onFailure(
        InvalidStateTransitionException $exception,
        /* StateContext in 4.0 */ $context,
        StateMachine $machine,
    ): string;
}
