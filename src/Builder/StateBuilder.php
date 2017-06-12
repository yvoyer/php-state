<?php

namespace Star\Component\State\Builder;

use Star\Component\State\StateMachine;
use Star\Component\State\States\CustomStateBuilder;
use Star\Component\State\States\StringState;
use Star\Component\State\TransitionRegistry;
use Star\Component\State\Transitions\FromToTransition;
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

    private function __construct()
    {
        $this->registry = new TransitionRegistry();
    }

    /**
     * @param string $name
     * @param string $from
     * @param string $to
     *
     * @return StateBuilder
     */
    public function allowTransition($name, $from, $to)
    {
        $this->registry->addTransition(
            new FromToTransition($name, new StringState($from), new StringState($to))
        );

        return $this;
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
     * @param CustomStateBuilder $builder
     *
     * @return StateBuilder
     */
    public function registerCustomState(CustomStateBuilder $builder)
    {
        $builder->registerTransitions($this->registry);

        return $this;
    }

    /**
     * @param string $currentState
     *
     * @return StateMachine
     */
    public function create($currentState)
    {
        Assert::string($currentState, "The current state name was expected to be a string. Got: %s.");
        return new StateMachine($currentState, $this->registry);
    }

    /**
     * @return StateBuilder
     */
    public static function build()
    {
        return new static();
    }
}
