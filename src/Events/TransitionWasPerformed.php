<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Events;

use Star\Component\State\State;
use Symfony\Component\EventDispatcher\Event;

final class TransitionWasPerformed extends Event
{
    /**
     * @var State
     */
    private $wasOn;

    /**
     * @var State
     */
    private $currentState;

    /**
     * @param State $wasOn
     * @param State $currentState
     */
    public function __construct(State $wasOn, State $currentState)
    {
        $this->wasOn = $wasOn;
        $this->currentState = $currentState;
    }

    /**
     * @return State
     */
    public function wasOn()
    {
        return $this->wasOn;
    }

    /**
     * @return State
     */
    public function currentState()
    {
        return $this->currentState;
    }
}
