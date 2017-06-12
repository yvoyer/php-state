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
    private $status = DoorState::UNLOCKED;

    public function isLocked()
    {
        return $this->state()->isInState(DoorState::LOCKED);
    }

    public function isUnlocked()
    {
        return $this->state()->isInState(DoorState::UNLOCKED);
    }

    public function handleIsTurnable()
    {
        return $this->state()->hasAttribute(DoorState::HANDLE_IS_TURNABLE);
    }

    public function lock()
    {
        $this->status = $this->state()->transitContext(DoorState::LOCK, $this);
    }

    public function unlock()
    {
        $this->status = $this->state()->transitContext(DoorState::UNLOCK, $this);
    }

    /**
     * @return StateMachine
     */
    private function state()
    {
        return StateBuilder::fromBuilder(new CustomFactory())->create($this->status);
    }
}
