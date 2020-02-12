<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class InvalidStateTransitionException extends \Exception
{
    /**
     * @param string $transition
     * @param string|object $context
     * @param string $currentState
     *
     * @return static
     */
    public static function notAllowedTransition(
        string $transition,
        $context,
        string $currentState
    ): self {
        if (\is_object($context)) {
            $context = \get_class($context);
        }

        return new static(
            \sprintf(
                "The transition '%s' is not allowed when context '%s' is in state '%s'.",
                $transition,
                $context,
                $currentState
            )
        );
    }
}
