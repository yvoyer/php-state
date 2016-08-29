<?php
/**
 * This file is part of the state-pattern project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class StateMachine
{
    /**
     * @param mixed $toState
     * @param StateContext $context
     */
    public function transitTo($toState, StateContext $context)
    {
        $context->setState(new MappingState($toState));
    }
}
