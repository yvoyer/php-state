<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Events;

final class StateEventStore
{
    /**
     * This event is performed before the transition is issued on the context.
     */
    const BEFORE_TRANSITION = 'before';

    /**
     * This event is performed after the transition is performed on the context.
     */
    const AFTER_TRANSITION = 'after';
}
