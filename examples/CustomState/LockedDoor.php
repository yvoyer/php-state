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
        return DoorCustomState::LOCKED;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        return $attribute === DoorCustomState::HANDLE_IS_TURNABLE;
    }
}
