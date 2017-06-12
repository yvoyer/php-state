<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\State;

abstract class DoorState implements State
{
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
}
