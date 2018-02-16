<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use Star\Component\State\StateRegistry;
use Star\Component\State\StateTransition;
use Star\Component\State\TransitionVisitor;
use Webmozart\Assert\Assert;

final class ManyToOneTransition implements StateTransition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var State
     */
    private $fromStates;

    /**
     * @var State
     */
    private $to;

    /**
     * @param string $name
     * @param State[] $fromStates
     * @param State $to
     */
    public function __construct($name, array $fromStates, State $to)
    {
        Assert::greaterThanEq(count($fromStates), 1, 'Expected at least %2$s state. Got: %s');
        Assert::allIsInstanceOf($fromStates, State::class);
        $this->name = $name;
        $this->fromStates = $fromStates;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param State $state
     *
     * @return bool
     */
    public function isAllowed(State $state)
    {
        foreach ($this->fromStates as $from) {
            if ($state->matchState($from)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param StateRegistry $registry
     */
    public function onRegister(StateRegistry $registry)
    {
        foreach ($this->fromStates as $from) {
            $from->register($registry);
        }
        $this->to->register($registry);
    }

    /**
     * @param StateContext $context
     */
    public function beforeStateChange(StateContext $context)
    {
    }

    /**
     * @param StateContext $context
     * @param StateMachine $machine
     */
    public function onStateChange(StateContext $context, StateMachine $machine)
    {
        $machine->setCurrentState($this->to);
    }

    /**
     * @param StateContext $context
     */
    public function afterStateChange(StateContext $context)
    {
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        $visitor->visitTransition($this->name);
        foreach ($this->fromStates as $state) {
            $state->acceptTransitionVisitorFrom($visitor);
        }
        $this->to->acceptTransitionVisitorTo($visitor);
    }
}
