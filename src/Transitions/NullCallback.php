<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class NullCallback implements TransitionCallback
{
    /**
     * @param InvalidStateTransitionException $exception
     * @param StateMachine $machine
     *
     * @return string The new state to move to on failure
     */
    public function onFailure(InvalidStateTransitionException $exception, StateMachine $machine)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }
}
