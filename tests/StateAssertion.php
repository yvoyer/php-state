<?php

namespace Star\Component\State;

trait StateAssertion
{
    /**
     * @param string $transition
     * @param string $context
     * @param string $currentState
     */
    protected function assertInvalidTransition($transition, $context, $currentState)
    {
        $this->setExpectedException(
            InvalidStateTransitionException::class,
            sprintf(
                "The transition '%s' is not allowed when context '%s' is in state '%s'.",
                $transition,
                $context,
                $currentState
            )
        );
    }
}
