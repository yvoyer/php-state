<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Events;

use Star\Component\State\State;
use Star\Component\State\StateContext;
use Symfony\Component\EventDispatcher\Event;

final class ContextTransitionWasRequested extends Event
{
    /**
     * @var StateContext
     */
    private $context;

    /**
     * @param StateContext $context
     */
    public function __construct(StateContext $context)
    {
        $this->context = $context;
    }

    /**
     * @return State
     */
    public function context()
    {
        return $this->context;
    }
}
