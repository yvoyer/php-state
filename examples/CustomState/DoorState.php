<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\State;

abstract class DoorState implements State
{
    const LOCK = 'lock';
    const UNLOCK = 'unlock';
    const LOCKED = 'locked';
    const UNLOCKED = 'unlocked';
    const HANDLE_IS_TURNABLE = 'handle_is_turnable';

    /**
     * @param State $state
     *
     * @return bool
     */
    public function matchState(State $state)
    {
        return $this->getName() === $state->getName();
    }

    /**
     * @param string $attribute
     */
    public function addAttribute($attribute)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        return $attribute === DoorState::HANDLE_IS_TURNABLE;
    }
}
