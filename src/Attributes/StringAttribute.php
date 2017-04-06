<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Attributes;

use Star\Component\State\StateAttribute;
use Webmozart\Assert\Assert;

final class StringAttribute implements StateAttribute
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
	    Assert::string($name, 'Attribute name must be a string. Got: %s');
	    Assert::string($value, 'Attribute value must be a string. Got: %s');

        $this->name = $name;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Returns whether the value match the attribute's value.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public function matchValue($value)
    {
        return $this->value === $value;
    }
}
