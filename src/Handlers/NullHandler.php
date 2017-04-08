<?php

namespace Star\Component\State\Handlers;

use Star\Component\State\FailureHandler;
use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateTransition;

final class NullHandler implements FailureHandler
{
    /**
     * @param StateTransition $transition
     * @param StateContext $context
     * @param State $current
     */
    public function beforeTransitionNotAllowed(StateTransition $transition, StateContext $context, State $current)
    {
        // do nothing
    }
}
