<?php declare(strict_types=1);

namespace Star\Component\State;

final class NotFoundException extends \Exception
{
    public static function stateNotFound(string $name): self
    {
        return new self(\sprintf("The state '%s' could not be found.", $name));
    }

    public static function transitionNotFound(string $name): self
    {
        return new self(sprintf("The transition '%s' could not be found.", $name));
    }
}
