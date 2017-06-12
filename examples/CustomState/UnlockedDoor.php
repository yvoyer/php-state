<?php

namespace Star\Component\State\Example\CustomState;

final class UnlockedDoor extends DoorState
{
    /**
     * The string value of the state
     *
     * @return string
     */
    public function getName()
    {
        return DoorState::UNLOCKED;
    }
}
