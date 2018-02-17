<?php

namespace Star\Component\State;

interface FailureHandler
{
    /**
     * @param string $transition
     * @param StateContext $context
     * @param string $current
     */
    public function beforeTransitionNotAllowed($transition, StateContext $context, $current);
}
