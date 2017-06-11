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
    private $status = 'unlocked';

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
        return $this->state()->hasAttribute('handle_is_turnable');
    }

    public function lock()
    {
        $this->status = $this->state()->transitContext('lock', $this);
    }

    public function unlock()
    {
        $this->status = $this->state()->transitContext('unlock', $this);
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
