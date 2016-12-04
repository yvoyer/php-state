<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

interface StateContext
{
    /**
     * @param State $state
     * @internal Public to be used by the State machine only.
     */
    public function setState(State $state);

    /**
     * @return State
     * @internal Public to be used by the State machine only.
     */
    public function getCurrentState();

    /**
     * Returns the alias used to identify custom events for transitions.
     *
     * @return string
     */
    public function contextAlias();
}
