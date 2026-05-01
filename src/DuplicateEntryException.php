<?php declare(strict_types=1);

namespace Star\Component\State;

use function sprintf;

final class DuplicateEntryException extends \LogicException
{
    public static function duplicateTransition(string $transition): self
    {
        return new self(
            sprintf("The transition '%s' is already registered.", $transition)
        );
    }
}
