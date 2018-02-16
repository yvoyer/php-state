<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use Star\Component\State\StateRegistry;
use Star\Component\State\States\StringState;
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
     * @var State[]
     */
    private $fromStates;

    /**
     * @var State
     */
    private $to;

    /**
     * @param string $name
     * @param string[] $fromStates
     * @param string $to
     */
    public function __construct($name, array $fromStates, $to)
    {
        Assert::greaterThanEq(count($fromStates), 1, 'Expected at least %2$s state. Got: %s');
        Assert::allString($fromStates);
        Assert::string($to);
        $this->name = $name;
        $this->fromStates = array_map(
            function ($fromName) {
                return new StringState($fromName);// todo Remove instance from here, use string only
            },
            $fromStates
        );
        $this->to = new StringState($to);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $state
     *
     * @return bool
     */
    public function isAllowed($state)
    {
        Assert::string($state);
        foreach ($this->fromStates as $from) {
            if ($state === $from->getName()) {
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
            $registry->registerState($from->getName(), []);
        }
        $registry->registerState($this->to->getName(), []);
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
            $visitor->visitFromState($state);
        }
        $visitor->visitToState($this->to);
    }
}
