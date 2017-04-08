<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Event;

/**
 * @experimental
 */
final class StateEventStore
{
    /**
     * This event is performed before any transition on the context.
     *
     * Event: TransitionWasRequested
     */
    const BEFORE_TRANSITION = 'star_state.before_transition';

    /**
     * This event is performed after any transition is executed on the context.
     *
     * Event: TransitionWasSuccessful
     */
    const AFTER_TRANSITION = 'star_state.after_transition';

}
