<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use Star\Component\State\StateTransition;
use Star\Component\State\TransitionRegistry;
use Webmozart\Assert\Assert;

final class FromToTransition implements StateTransition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var State
     */
    private $from;

    /**
     * @var State
     */
    private $to;

    /**
     * @param string $name
     * @param State $from
     * @param State $to
     */
    public function __construct($name, State $from, State $to)
    {
        Assert::string($name, 'Transition name should be string, "%s" given.');
        $this->name = $name;
        $this->from = $from;
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
     * @param StateMachine $machine
     *
     * @return bool
     */
    public function isAllowed(StateMachine $machine)
    {
        return $machine->isInState($this->from->getName());
    }

    /**
     * @param TransitionRegistry $registry
     */
    public function onRegister(TransitionRegistry $registry)
    {
        $registry->addState($this->from);
        $registry->addState($this->to);
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
}
