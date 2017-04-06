<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\States;

use Star\Component\State\Attributes\StringAttribute;
use Star\Component\State\State;
use Star\Component\State\StateAttribute;
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
        $this->name = $name;
        foreach ($attributes as $key => $value) {
            $this->addAttribute(new StringAttribute($key, $value));
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
	    if (! $state instanceof $this) {
		    return false;
	    }

	    if ($state->name() !== $this->name()) {
		    return false;
	    }

	    if (
		    count(array_diff($state->attributes, $this->attributes)) > 0
		    || count(array_diff($this->attributes, $state->attributes)) > 0
	    ) {
		    return false;
	    }

        return true;
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

	/**
	 * @param StateAttribute $attribute
	 *
	 * @return State
	 */
	private function addAttribute(StateAttribute $attribute)
	{
		$this->attributes[$this->name()] = $attribute;
	}
}
