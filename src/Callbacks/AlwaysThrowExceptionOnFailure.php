<?php

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class AlwaysThrowExceptionOnFailure implements TransitionCallback
{
    /**
     * @param mixed $context
     * @param StateMachine $machine
     */
    public function beforeStateChange($context, StateMachine $machine)
    {
    }

    /**
     * @param mixed $context
     * @param StateMachine $machine
     */
    public function afterStateChange($context, StateMachine $machine)
    {
    }

    /**
     * @param InvalidStateTransitionException $exception
     * @param mixed $context
     * @param StateMachine $machine
     *
     * @return string
     * @throws InvalidStateTransitionException
     */
    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine)
    {
        throw $exception;
    }
}
