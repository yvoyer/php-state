<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\States\StateFactory;
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
final class CustomFactory implements StateFactory
{
    /**
     * Register your custom states.
     *
     * @param TransitionRegistry $registry
     */
    public function registerTransitions(TransitionRegistry $registry)
    {
        $registry->addTransition(new LockTransition());
        $registry->addTransition(new UnlockTransition());
    }
}
