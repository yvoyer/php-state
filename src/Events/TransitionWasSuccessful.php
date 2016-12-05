<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Events;

use Star\Component\State\State;
use Symfony\Component\EventDispatcher\Event;

final class TransitionWasSuccessful extends Event
{
    /**
     * @var State
     */
    private $before;

    /**
     * @var State
     */
    private $current;

    /**
     * @param State $before
     * @param State $current
     */
    public function __construct(State $before, State $current)
    {
        $this->before = $before;
        $this->current = $current;
    }

    /**
     * @return State
     */
    public function before()
    {
        return $this->before;
    }

    /**
     * @return State
     */
    public function current()
    {
        return $this->current;
    }
}
