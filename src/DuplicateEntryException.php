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

    /**
     * @param State $state
     *
     * @return DuplicateEntryException
     */
    public static function duplicateState(State $state)
    {
        return new self(
            sprintf(
                'The state "%s" is already registered, maybe there is a mismatch with the attributes.',
                $state->getName()
            )
        );
    }
}
