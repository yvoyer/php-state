<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Event;

use Star\Component\State\StateTransition;
use Symfony\Component\EventDispatcher\Event;

/**
 * @experimental
 */
final class TransitionWasSuccessful extends Event
{
    /**
     * @var StateTransition
     */
    private $transition;

    /**
     * @param StateTransition $transition
     */
    public function __construct(StateTransition $transition)
    {
        $this->transition = $transition;
    }

    /**
     * @return StateTransition
     */
    public function transition()
    {
        return $this->transition;
    }
}
