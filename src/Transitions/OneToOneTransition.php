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
        $this->from = new StringState($from);
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
     * @param string $from
     *
     * @return bool
     */
    public function isAllowed($from)
    {
        Assert::string($from);
        return $from === $this->from->getName();
    }

    /**
     * @param StateRegistry $registry
     */
    public function onRegister(StateRegistry $registry)
    {
        $this->from->register($registry);
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
        $this->from->acceptTransitionVisitorFrom($visitor);
        $this->to->acceptTransitionVisitorTo($visitor);
    }
}
