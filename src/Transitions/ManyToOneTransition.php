<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use Star\Component\State\StateRegistry;
use Star\Component\State\States\ArrayState;
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
    private $from;

    /**
     * @var State
     */
    private $to;

    /**
     * @param string $name
     * @param State[] $from
     * @param State $to
     */
    public function __construct($name, array $from, State $to)
    {
        Assert::greaterThanEq(count($from), 1, 'Expected at least %2$s state. Got: %s');
        Assert::allIsInstanceOf($from, State::class);
        $this->name = $name;
        $this->from = new ArrayState($from);
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
     * @param State $from
     *
     * @return bool
     */
    public function isAllowed(State $from)
    {
        return $this->from->matchState($from);
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
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }
}
