<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

final class Door implements StateContext
{
    private $status;

    public function isLocked()
    {
        return $this->state()->isInState('locked');
    }

    public function isUnlocked()
    {
        return $this->state()->isInState('unlocked');
    }

    public function handleIsTurnable()
    {
        return $this->state()->hasAttribute('turn_handler');
    }

    public function lock()
    {
        $this->state()->transitContext('lock', $this);
    }

    public function unlock()
    {
        $this->state()->transitContext('unlock', $this);
    }

    /**
     * @return StateMachine
     */
    private function state()
    {
        return StateBuilder::build()->registerCustomState(
            new DoorStateMetadata(),
            function ($initialState) {
                $this->status = $initialState;
            }
        );
    }
}
