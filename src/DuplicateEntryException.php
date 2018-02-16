<?php

namespace Star\Component\State;

final class DuplicateEntryException extends \LogicException {
    /**
     * @param StateTransition $transition
     *
     * @return DuplicateEntryException
     */
    public static function duplicateTransition(StateTransition $transition)
    {
        return new self(
            sprintf("The transition '%s' is already registered.", $transition->getName())
        );
    }
}
