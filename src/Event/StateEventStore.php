<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Event;

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

    /**
     * This is the format for the before transition, of a specific transition.
     * The first argument is the context alias.
     * The second argument is the context's transition.
     *
     * ie. "star_state.before.car.stop": would be triggered before the car context
     * is transitioned to the stop transition ("started" to "stopped").
     *
     * Event: ContextTransitionWasRequested
     */
    const CUSTOM_EVENT_BEFORE = 'star_state.before.%s.%s';

    /**
     * This is the format for the after transition, of a specific transition.
     * The first argument is the context alias.
     * The second argument is the context's transition.
     *
     * ie. "star_state.after.car.stop": would be triggered after the car context
     * is transitioned to the stop transition ("started" to "stopped").
     *
     * Event: ContextTransitionWasSuccessful
     */
    const CUSTOM_EVENT_AFTER = 'star_state.after.%s.%s';
}
