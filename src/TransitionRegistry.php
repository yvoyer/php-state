<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\States\StringState;
use Webmozart\Assert\Assert;

final class TransitionRegistry implements StateRegistry
{
    /**
     * @var StateTransition[]
     */
    private $transitions = [];

    /**
     * @var StateRegistry
     */
    private $states;

    public function __construct()
    {
        $this->states = [];
    }

    /**
     * @param StateTransition $transition
     */
    public function addTransition(StateTransition $transition)
    {
        if (isset($this->transitions[$transition->getName()])) {
            throw DuplicateEntryException::duplicateTransition($transition);
        }

        $this->transitions[$transition->getName()] = $transition;
        $transition->onRegister($this);
    }

    /**
     * @param string $name The transition name
     *
     * @return StateTransition
     * @throws NotFoundException
     */
    public function getTransition($name)
    {
        Assert::string($name);
        $transition = null;
        if (isset($this->transitions[$name])) {
            $transition = $this->transitions[$name];
        }

        if (! $transition) {
            throw NotFoundException::transitionNotFound($name);
        }

        return $transition;
    }

    /**
     * @param string $name
     * @return State
     * @throws NotFoundException
     */
    public function getState($name)
    {
        Assert::string($name);
        if (! isset($this->states[$name])) {
            throw NotFoundException::stateNotFound($name);
        }

        return $this->states[$name];
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        foreach ($this->transitions as $transition) {
            $transition->acceptTransitionVisitor($visitor, $this);
        }
    }

    /**
     * @param StateVisitor $visitor
     */
    public function acceptStateVisitor(StateVisitor $visitor)
    {
        foreach ($this->transitions as $transition) {
            $transition->acceptStateVisitor($visitor, $this);
        }
    }

    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function registerState($name, array $attributes = [])
    {
        $state = new StringState($name, $attributes);
        if (isset($this->states[$name])) {
            $state = $this->getState($name);
            $state->addAttributes($attributes);
        }

        $this->states[$name] = $state;
    }
}
