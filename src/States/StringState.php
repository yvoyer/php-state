<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\States;

use Star\Component\State\State;
use Webmozart\Assert\Assert;

final class StringState implements State
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $attributes = [];

    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function __construct($name, array $attributes = [])
    {
        Assert::string($name, 'The state was expected to be a string, "%s" given.');
        Assert::allString($attributes, 'The state attributes must be strings, "" given.');
        $this->name = $name;
        $this->attributes = array_values(array_unique($attributes));
    }

    /**
     * The string value of the state
     *
     * @return string
     */
    public function toString()
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
        if (! $state instanceof $this) {
            return false;
        }

        if ($state->toString() !== $this->toString()) {
            return false;
        }

        return count(array_diff($state->attributes, $this->attributes)) === 0;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        return false !== array_search($attribute, $this->attributes);
    }
}
