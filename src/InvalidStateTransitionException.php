<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

class InvalidStateTransitionException extends \Exception
{
    /**
     * @param StateTransition $transition
     * @param StateContext $context
     * @param State $currentState
     *
     * @return static
     */
    public static function notAllowedTransition(
        StateTransition $transition,
        StateContext $context,
        State $currentState
    ) {
        return new static(
            sprintf(
                "The transition '%s' is not allowed when context '%s' is in state '%s'.",
                $transition->getName(),
                get_class($context),
                $currentState->getName()
            )
        );
    }
}
