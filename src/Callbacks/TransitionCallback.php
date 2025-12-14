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
     */
    public function beforeStateChange(
        $context,
        StateMachine $machine,
    ): void;

    /**
     * @param mixed|StateContext $context
     * @param StateMachine $machine
     */
    public function afterStateChange(
        $context,
        StateMachine $machine,
    ): void;

    /**
     * @param InvalidStateTransitionException $exception
     * @param mixed|StateContext $context
     * @param StateMachine $machine
     *
     * @return string The new state to move to on failure
     */
    public function onFailure(
        InvalidStateTransitionException $exception,
        $context,
        StateMachine $machine,
    ): string;
}
