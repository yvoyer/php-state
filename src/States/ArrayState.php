<?php

namespace Star\Component\State\States;

use Star\Component\State\State;
use Star\Component\State\StateRegistry;
use Star\Component\State\TransitionVisitor;
use Webmozart\Assert\Assert;

final class ArrayState implements State
{
    /**
     * @var State[]
     */
    private $states;

    /**
     * @param State[] $states
     */
    public function __construct(array $states)
    {
        Assert::allIsInstanceOf($states, State::class);
        $this->states = $states;
    }

    /**
     * The string value of the state
     *
     * @return string
     */
    public function getName()
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    /**
     * @param State $state
     *
     * @return bool
     */
    public function matchState(State $state)
    {
        foreach ($this->states as $_s) {
            if ($_s->matchState($state)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    /**
     * @param string $attribute
     */
    public function addAttribute($attribute)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    /**
     * @param StateRegistry $registry
     */
    public function register(StateRegistry $registry)
    {
        foreach ($this->states as $state) {
            $state->register($registry);
        }
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitorFrom(TransitionVisitor $visitor)
    {
        foreach ($this->states as $state) {
            $visitor->visitFromState($state);
        }
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitorTo(TransitionVisitor $visitor)
    {
        foreach ($this->states as $state) {
            $visitor->visitToState($state);
        }
    }
}
