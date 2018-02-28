<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class AlwaysThrowExceptionOnFailure implements TransitionCallback
{
    /**
     * @param InvalidStateTransitionException $exception
     * @param StateMachine $machine
     *
     * @return string The new state to move to on failure
     * @throws InvalidStateTransitionException
     */
    public function onFailure(InvalidStateTransitionException $exception, StateMachine $machine)
    {
        throw $exception;
    }
}
