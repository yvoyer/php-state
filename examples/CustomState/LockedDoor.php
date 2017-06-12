<?php

namespace Star\Component\State\Example\CustomState;

final class LockedDoor extends DoorState
{
    /**
     * The string value of the state
     *
     * @return string
     */
    public function getName()
    {
        return DoorState::LOCKED;
    }
}
