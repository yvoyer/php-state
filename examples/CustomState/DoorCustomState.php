<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\States\CustomStateBuilder;
use Star\Component\State\TransitionRegistry;

/**
 * Transitions
 * +-----------+------------+------------+
 * | from / to |   locked   |  unlocked  |
 * +===========+============+============+
 * | locked    | disallowed | lock       |
 * +-----------+------------+------------+
 * | unlocked  | unlock     | disallowed |
 * +-----------+------------+------------+
 *
 * Attributes
 * +-------------------+---------------------+
 * | state / attribute | handle_is_turnable  |
 * +===================+=====================+
 * | locked            |       true          |
 * +-------------------+---------------------+
 * | unlocked          |       true          |
 * +-------------------+---------------------+
 */
final class DoorCustomState implements CustomStateBuilder
{
    const LOCK = 'lock';
    const UNLOCK = 'unlock';
    const LOCKED = 'locked';
    const UNLOCKED = 'unlocked';
    const HANDLE_IS_TURNABLE = 'handle_is_turnable';

    /**
     * Register your custom states.
     *
     * @param TransitionRegistry $registry
     */
    public function registerTransitions(TransitionRegistry $registry)
    {
        $registry->addState(new LockedDoor());
        $registry->addState(new UnlockedDoor());
        $registry->addTransition(new LockTransition());
        $registry->addTransition(new UnlockTransition());
    }
}
