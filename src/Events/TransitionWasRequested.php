<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Events;

use Star\Component\State\State;
use Symfony\Component\EventDispatcher\Event;

final class TransitionWasRequested extends Event
{
    /**
     * @var State
     */
    private $from;

    /**
     * @var State
     */
    private $to;

    /**
     * @param State $from
     * @param State $to
     */
    public function __construct(State $from, State $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return State
     */
    public function from()
    {
        return $this->from;
    }

    /**
     * @return State
     */
    public function to()
    {
        return $this->to;
    }
}
