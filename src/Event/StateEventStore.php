<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Event;

final class StateEventStore
{
    /**
     * This event is performed before any transition on the context.
     *
     * @see TransitionWasRequested
     */
    const BEFORE_TRANSITION = 'star_state.before_transition';

    /**
     * This event is performed after any transition is executed on the context.
     *
     * @see TransitionWasSuccessful
     */
    const AFTER_TRANSITION = 'star_state.after_transition';

    /**
     * This event is performed before the transition exception is triggered.
     *
     * @see TransitionWasFailed
     */
    const FAILURE_TRANSITION = 'star_state.transition_failure';
}
