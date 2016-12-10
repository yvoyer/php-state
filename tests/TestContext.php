<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class TestContext implements StateContext
{
    const  ALIAS = 'context';

    /**
     * @var string
     */
    private $current;

    /**
     * @param string $initial
     */
    private function __construct($initial)
    {
        $this->current = $initial;
    }

    public function setState(State $state)
    {
        $this->current = $state->name();
    }

    public function getCurrentState()
    {
        return new StringState($this->current);
    }

    public static function fromString($state = 'from')
    {
        return new self($state);
    }

    /**
     * @return string
     */
    public function contextAlias()
    {
        return self::ALIAS;
    }
}
