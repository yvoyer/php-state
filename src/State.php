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
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute);

    /**
     * @param string $attribute
     */
    public function addAttribute($attribute);

    /**
     * @param StateVisitor $visitor
     */
    public function acceptStateVisitor(StateVisitor $visitor);
}
