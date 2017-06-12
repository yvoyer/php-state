<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

final class Door implements StateContext
{
    /**
     * @var string
     */
    private $status = DoorCustomState::UNLOCKED;

    public function isLocked()
    {
        return $this->state()->isInState(DoorCustomState::LOCKED);
    }

    public function isUnlocked()
    {
        return $this->state()->isInState(DoorCustomState::UNLOCKED);
    }

    public function handleIsTurnable()
    {
        return $this->state()->hasAttribute(DoorCustomState::HANDLE_IS_TURNABLE);
    }

    public function lock()
    {
        $this->status = $this->state()->transitContext(DoorCustomState::LOCK, $this);
    }

    public function unlock()
    {
        $this->status = $this->state()->transitContext(DoorCustomState::UNLOCK, $this);
    }

    /**
     * @return StateMachine
     */
    private function state()
    {
        return StateBuilder::build()
            ->registerCustomState(new DoorCustomState())
            ->create($this->status)
        ;
    }
}
