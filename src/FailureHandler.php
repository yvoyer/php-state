<?php

namespace Star\Component\State;

interface FailureHandler
{
    /**
     * @param StateTransition $transition
     * @param StateContext $context
     * @param State $current
     */
    public function beforeTransitionNotAllowed(StateTransition $transition, StateContext $context, State $current);
}
