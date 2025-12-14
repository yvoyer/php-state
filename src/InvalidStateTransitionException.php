<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Context\ObjectAdapterContext;
use Star\Component\State\Context\StringAdapterContext;
use function is_scalar;
use function sprintf;

final class InvalidStateTransitionException extends \Exception
{
    /**
     * @param string $transition
     * @param string|object|StateContext $context
     * @param string $currentState
     *
     * @return static
     */
    public static function notAllowedTransition(
        string $transition,
        $context,
        string $currentState
    ): self {
        if (is_scalar($context)) {
            $context = new StringAdapterContext((string) $context, true);
        }

        if (!$context instanceof StateContext) {
            $context = new ObjectAdapterContext($context, true);
        }

        // todo statte context should have a toString()

        return new self(
            sprintf(
                "The transition '%s' is not allowed when context '%s' is in state '%s'.",
                $transition,
                $context->toStateContextIdentifier(),
                $currentState
            )
        );
    }
}
