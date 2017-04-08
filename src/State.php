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
    public function getName();

    /**
     * @param State $state
     *
     * @return bool
     */
    public function matchState(State $state);

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute);

    /**
     * @param string $attribute
     */
    public function addAttribute($attribute);
}
