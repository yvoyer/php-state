<?php

namespace Star\Component\State\Builder;

use Star\Component\State\EventRegistry;
use Star\Component\State\Port\Symfony\EventDispatcherAdapter;
use Star\Component\State\StateMachine;
use Star\Component\State\StateTransition;
use Star\Component\State\TransitionRegistry;
use Star\Component\State\Transitions\ManyToOneTransition;
use Star\Component\State\Transitions\OneToOneTransition;
use Webmozart\Assert\Assert;

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
            $transition = new ManyToOneTransition($from, $to);
        } else {
            $transition = new OneToOneTransition($from, $to);
        }

        $this->allowCustomTransition($name, $transition);

        return $this;
    }

    /**
     * @param string $name
     * @param StateTransition $transition
     */
    public function allowCustomTransition($name, StateTransition $transition)
    {
        Assert::string($name);
        $this->registry->addTransition($name, $transition);
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
            $state = $this->registry->getState($stateName);
            $state->addAttribute($attribute);
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
