<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Attribute\StateAttribute;

interface State
{
    /**
     * The string value of the state
     *
     * @return string
     */
    public function name();

    /**
     * @param State $state
     *
     * @return bool
     */
    public function matchState(State $state);

    /**
     * @param StateAttribute $attribute
     */
    public function addAttribute(StateAttribute $attribute);

    /**
     * @param StateAttribute $attribute
     *
     * @return bool
     */
    public function hasAttribute(StateAttribute $attribute);
}
