<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Attribute;

interface StateAttribute
{
    /**
     * @return string
     */
    public function name();

    /**
     * Returns whether the value match the attribute's value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matchValue($value);
}
