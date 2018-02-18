<?php

namespace Star\Component\State;

final class DuplicateEntryException extends \LogicException
{
    /**
     * @param string $transition
     *
     * @return DuplicateEntryException
     */
    public static function duplicateTransition($transition)
    {
        return new self(
            sprintf("The transition '%s' is already registered.", $transition)
        );
    }
}
