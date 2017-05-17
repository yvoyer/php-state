<?php

namespace Star\Component\State\Example\CustomState;

abstract class DoorState
{
    public function isLocked()
    {
        return false;
    }

    public function isUnlocked()
    {
        return false;
    }

    public function lock(Door $door)
    {
    }

    public function unlock(Door $door)
    {
    }
}
