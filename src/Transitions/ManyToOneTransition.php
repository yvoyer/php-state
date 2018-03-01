<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateRegistry;
use Star\Component\State\StateTransition;
use Star\Component\State\StateVisitor;
use Star\Component\State\TransitionVisitor;
use Webmozart\Assert\Assert;

final class ManyToOneTransition implements StateTransition
{
    /**
     * @var string[]
     */
    private $fromStates;

    /**
     * @var string
     */
    private $to;

    /**
     * @param string[] $fromStates
     * @param string $to
     */
    public function __construct(array $fromStates, $to)
    {
        Assert::greaterThanEq(count($fromStates), 1, 'Expected at least %2$s state. Got: %s');
        Assert::allString($fromStates);
        Assert::string($to);
        $this->fromStates = $fromStates;
        $this->to = $to;
    }

    /**
     * @param string $state
     *
     * @return bool
     */
    public function isAllowed($state)
    {
        Assert::string($state);
        return in_array($state, $this->fromStates, true);
    }

    /**
     * @param RegistryBuilder $registry
     */
    public function onRegister(RegistryBuilder $registry)
    {
        foreach ($this->fromStates as $from) {
            $registry->registerState($from, []);
        }

        $registry->registerState($this->to, []);
    }

    public function getDestinationState()
    {
        return $this->to;
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        foreach ($this->fromStates as $from) {
            $visitor->visitFromState($from);
        }

        $visitor->visitToState($this->to);
    }

    /**
     * @param StateVisitor $visitor
     * @param StateRegistry $registry
     */
    public function acceptStateVisitor(StateVisitor $visitor, StateRegistry $registry)
    {
        foreach ($this->fromStates as $from) {
            $registry->getState($from)->acceptStateVisitor($visitor);
        }

        $registry->getState($this->to)->acceptStateVisitor($visitor);
    }
}
