<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Webmozart\Assert\Assert;

final class TransitionRegistry
{
    /**
     * @var StateTransition[]
     */
    private $transitions = [];

    /**
     * @var State[]
     */
    private $states = [];

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
        Assert::string($name, 'The state name was expected to be a string. Got: %s');
        if (! isset($this->states[$name])) {
            throw NotFoundException::stateNotFound($name);
        }

        return $this->states[$name];
    }

    /**
     * @param State $state
     */
    public function addState(State $state)
    {
        $name = $state->getName();
        if (! isset($this->states[$name])) {
            $this->states[$name] = $state;
        }

        if (! $state->matchState($this->states[$name])) {
            throw DuplicateEntryException::duplicateState($state);
        }
    }
}
