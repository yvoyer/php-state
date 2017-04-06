<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class TestContext implements StateContext
{
    /**
     * @var string
     */
    private $current;

    /**
     * @param string $initial
     */
    public function __construct($initial)
    {
        $this->current = $initial;
    }

    public function setState(State $state)
    {
        $this->current = $state->name();
    }
}
