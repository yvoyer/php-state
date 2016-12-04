<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Webmozart\Assert\Assert;

final class StringState implements State
{
    /**
     * @var string
     */
    private $id;

    public function __construct($id)
    {
        Assert::string($id, "The status was expected to be a string, '%s' given.");

        $this->id = $id;
    }

    /**
     * The string value of the state
     *
     * @return string
     */
    public function toString()
    {
        return $this->id;
    }

    /**
     * @param State $state
     *
     * @return bool
     */
    public function matchState(State $state)
    {
        return $state instanceof $this && $this->id === $state->id;
    }
}
