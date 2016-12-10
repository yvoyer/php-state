<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Builder;

use Star\Component\State\Attribute\StateAttribute;
use Star\Component\State\Attribute\StringAttribute;

final class AttributeBuilder
{
    /**
     * @param string $name
     * @param mixed $value
     *
     * @return StateAttribute
     */
    public function attribute($name, $value = null)
    {
        return new StringAttribute($name, $value);
    }
}
