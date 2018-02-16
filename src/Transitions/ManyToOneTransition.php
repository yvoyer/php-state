<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use Star\Component\State\StateRegistry;
use Star\Component\State\StateTransition;
use Star\Component\State\StateVisitor;
use Star\Component\State\TransitionVisitor;
use Webmozart\Assert\Assert;

final class ManyToOneTransition implements StateTransition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $fromStates;

    /**
     * @var string
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
     * @param string $state
     *
     * @return bool
     */
    public function isAllowed($state)
    {
        Assert::string($state);
        foreach ($this->fromStates as $from) {
            if ($state === $from) {
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
            $registry->registerState($from, []);
        }
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
        foreach ($this->fromStates as $from) {
            $visitor->visitFromState($registry->getState($from));
        }

        $visitor->visitToState($registry->getState($this->to));
    }

    /**
     * @param StateVisitor $visitor
     * @param StateRegistry $registry
     */
    public function acceptStateVisitor(StateVisitor $visitor, StateRegistry $registry)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
        foreach ($this->fromStates as $from) {
            $registry->getState($from)->acceptStateVisitor($visitor);
        }

        $registry->getState($this->to)->acceptStateVisitor($visitor);
    }
}
