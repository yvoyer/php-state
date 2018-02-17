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
     * @param string $transition
     * @param StateContext $context
     * @param string $currentState
     *
     * @return static
     */
    public static function notAllowedTransition(
        $transition,
        StateContext $context,
        $currentState
    ) {
        return new static(
            sprintf(
                "The transition '%s' is not allowed when context '%s' is in state '%s'.",
                $transition,
                get_class($context),
                $currentState
            )
        );
    }
}
