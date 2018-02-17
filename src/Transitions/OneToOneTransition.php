<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\State;
use Star\Component\State\StateMachine;
use Star\Component\State\StateRegistry;
use Star\Component\State\StateTransition;
use Star\Component\State\StateVisitor;
use Star\Component\State\TransitionVisitor;
use Webmozart\Assert\Assert;

final class OneToOneTransition implements StateTransition
{
    /**
     * @var State
     */
    private $from;

    /**
     * @var State
     */
    private $to;

    /**
     * @param string $from
     * @param string $to
     */
    public function __construct($from, $to)
    {
        Assert::string($from);
        Assert::string($to);
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @param string $from
     *
     * @return bool
     */
    public function isAllowed($from)
    {
        Assert::string($from);
        return $from === $this->from;
    }

    /**
     * @param RegistryBuilder $registry
     */
    public function onRegister(RegistryBuilder $registry)
    {
        $registry->registerState($this->from, []);
        $registry->registerState($this->to, []);
    }

    /**
     * @param mixed $context
     */
    public function beforeStateChange($context)
    {
    }

    /**
     * @param StateMachine $machine
     */
    public function onStateChange(StateMachine $machine)
    {
        $machine->setCurrentState($this->to);
    }

    /**
     * @param mixed $context
     */
    public function afterStateChange($context)
    {
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        $visitor->visitFromState($this->from);
        $visitor->visitToState($this->to);
    }

    /**
     * @param StateVisitor $visitor
     * @param StateRegistry $registry
     */
    public function acceptStateVisitor(StateVisitor $visitor, StateRegistry $registry)
    {
        $registry->getState($this->from)->acceptStateVisitor($visitor);
        $registry->getState($this->to)->acceptStateVisitor($visitor);
    }
}
