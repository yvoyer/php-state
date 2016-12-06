<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

interface StateTransition
{
    /**
     * @return string
     */
    public function name();

    /**
     * @param State $from
     *
     * @return bool
     */
    public function hasChanged(State $from);

    /**
     * @param StateContext $context
     *
     * @return bool
     */
    public function isAllowed(StateContext $context);

    /**
     * @param StateContext $context
     */
    public function applyStateChange(StateContext $context);
}
