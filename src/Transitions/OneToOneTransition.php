<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use Star\Component\State\StateRegistry;
use Star\Component\State\StateTransition;
use Star\Component\State\StateVisitor;
use Star\Component\State\TransitionVisitor;
use Webmozart\Assert\Assert;

final class OneToOneTransition implements StateTransition
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
     * @param string $from
     * @param string $to
     */
    public function __construct($name, $from, $to)
    {
        Assert::string($name, 'Transition name should be string, "%s" given.');
        Assert::string($from);
        Assert::string($to);
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
     * @param StateRegistry $registry
     */
    public function onRegister(StateRegistry $registry)
    {
        $registry->registerState($this->from, []);
        $registry->registerState($this->to, []);
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
     * @param StateRegistry $registry
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor, StateRegistry $registry)
    {
        $visitor->visitTransition($this->name);
        $visitor->visitFromState($registry->getState($this->from));
        $visitor->visitToState($registry->getState($this->to));
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
