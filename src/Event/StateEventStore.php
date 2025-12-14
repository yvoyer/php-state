<?php declare(strict_types=1);

namespace Star\Component\State\Event;

use function get_class;

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

    public static function eventNameFromClass(StateEvent $event): string
    {
        return match (get_class($event)) {
            TransitionWasRequested::class => self::BEFORE_TRANSITION,
            TransitionWasSuccessful::class => self::AFTER_TRANSITION,
            TransitionWasFailed::class => self::FAILURE_TRANSITION,
        };
    }
}
