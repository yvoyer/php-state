<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Attribute\StateAttribute;
use Webmozart\Assert\Assert;

final class StringState implements State
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var StateAttribute[]
     */
    private $attributes = [];

    /**
     * @param string $name
     * @param StateAttribute[] $attributes
     */
    public function __construct($name, array $attributes = [])
    {
        Assert::string($name, "The status was expected to be a string, '%s' given.");
        Assert::allIsInstanceOf($attributes, StateAttribute::class);

        $this->name = $name;
        foreach ($attributes as $attribute) {
            $this->attributes[$attribute->name()] = $attribute;
        }
    }

    /**
     * The string value of the state
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param State $state
     *
     * @return bool
     */
    public function matchState(State $state)
    {
        return $state->name() === $this->name();
    }

    /**
     * @param StateAttribute $attribute
     *
     * @return State
     */
    public function addAttribute(StateAttribute $attribute)
    {
        return new self($this->name(), array_merge($this->attributes, [$attribute]));
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        return isset($this->attributes[$attribute]);
    }
}
