<?php

namespace Star\Component\State;

interface FailureHandler
{
    /**
     * @param string $transition
     * @param mixed $context
     * @param string $current
     */
    public function beforeTransitionNotAllowed($transition, $context, $current);
}
