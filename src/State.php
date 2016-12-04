<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

interface State
{
    /**
     * The string value of the state
     *
     * @return string
     */
    public function toString();

    /**
     * @param State $state
     *
     * @return bool
     */
    public function matchState(State $state);
}
