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
     * @var State[]
     */
    private $states = [];

    /**
     * @param string $name
     * @param StateTransition $transition
     */
    public function addTransition($name, StateTransition $transition)
    {
        Assert::string($name);
        if (isset($this->transitions[$name])) {
            throw DuplicateEntryException::duplicateTransition($name);
        }

        $this->transitions[$name] = $transition;
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
        if (! $this->hasState($name)) {
            throw NotFoundException::stateNotFound($name);
        }

        return $this->states[$name];
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasState($name)
    {
        return isset($this->states[$name]);
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        foreach ($this->transitions as $name => $transition) {
            $visitor->visitTransition($name);
            $transition->acceptTransitionVisitor($visitor);
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
        if ($this->hasState($name)) {
            $state = $this->getState($name);
        }

        foreach ($attributes as $attribute) {
            $state->addAttribute($attribute);
        }

        $this->states[$name] = $state;
    }
}
