<?php

namespace Star\Component\State\Builder;

use Star\Component\State\EventRegistry;
use Star\Component\State\Port\Symfony\EventDispatcherAdapter;
use Star\Component\State\StateMachine;
use Star\Component\State\StateTransition;
use Star\Component\State\TransitionRegistry;
use Star\Component\State\Transitions\ManyToOneTransition;
use Star\Component\State\Transitions\OneToOneTransition;

/**
 * Tool to build the StateMachine.
 */
final class StateBuilder
{
    /**
     * @var TransitionRegistry
     */
    private $registry;

    /**
     * @var EventRegistry
     */
    private $listeners;

    public function __construct()
    {
        $this->registry = new TransitionRegistry();
        $this->listeners = new EventDispatcherAdapter();
    }

    /**
     * @param string $name
     * @param string|string[] $from
     * @param string $to
     *
     * @return StateBuilder
     */
    public function allowTransition($name, $from, $to)
    {
        if (is_array($from)) {
            $transition = new ManyToOneTransition($name, $from, $to);
        } else {
            $transition = new OneToOneTransition($name, $from, $to);
        }

        $this->allowCustomTransition($transition);

        return $this;
    }

    /**
     * @param StateTransition $transition
     */
    public function allowCustomTransition(StateTransition $transition)
    {
        $this->registry->addTransition($transition);
    }

    /**
     * @param string $attribute The attribute
     * @param string|string[] $states The list of states that this attribute applies to
     *
     * @return StateBuilder
     */
    public function addAttribute($attribute, $states)
    {
        $states = (array) $states;
        foreach ($states as $stateName) {
            $this->registry->addAttribute($stateName, $attribute);
        }

        return $this;
    }

    /**
     * @param string $currentState
     *
     * @return StateMachine
     */
    public function create($currentState)
    {
        return new StateMachine($currentState, $this->registry, $this->listeners);
    }

    /**
     * @return StateBuilder
     */
    public static function build()
    {
        return new static();
    }
}
