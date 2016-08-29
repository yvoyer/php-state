<?php
/**
 * This file is part of the state-pattern project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class StateBuilder
{
    /**
     * @var array
     */
    private $mapping = [];

    /**
     * @var StateMachine
     */
    private $machine;

    /**
     * @param StateMachine $machine
     */
    public function __construct(StateMachine $machine)
    {
        $this->machine = $machine;
    }

    /**
     * @param mixed $from
     * @param mixed $to
     * @param string $contextClass
     *
     * @return StateBuilder
     */
    public function allow($from, $to, $contextClass)
    {
        $this->mapping[$contextClass][$from] = $to;

        return $this;
    }

    /**
     * @return StateMachine
     */
    public function getStateMachine()
    {
        return $this->machine;
    }

    /**
     * @return StateBuilder
     */
    public static function create()
    {
        return new self(new StateMachine());
    }
}
