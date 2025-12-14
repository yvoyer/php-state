<?php declare(strict_types=1);

namespace Star\Component\State;

use Exception;
use function sprintf;

final class InvalidStateTransitionException extends Exception
{
    public static function notAllowedTransition(
        string $transition,
        StateContext $context,
        string $currentState
    ): self {
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
