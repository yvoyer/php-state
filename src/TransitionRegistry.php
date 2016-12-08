<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Attribute\StateAttribute;
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
     * @var FailureHandler
     */
    private $failureHandler;

    public function __construct()
    {
        $this->failureHandler = new AlwaysThrowException();
    }

    /**
     * @param string $context
     * @param StateTransition $transition
     */
    public function addTransition($context, StateTransition $transition)
    {
        $name = $transition->name();
        $this->assertTransitionNameIsValid($name);
        $this->assertContextNameIsValid($context);

        $this->transitions[$context][$name] = $transition;
        $transition->register($context, $this);
    }

    /**
     * @param string $name The transition name
     * @param string $context The context alias
     *
     * @return StateTransition
     * @throws NotFoundException
     */
    public function getTransition($name, $context)
    {
        $this->assertTransitionNameIsValid($name);
        $this->assertContextNameIsValid($context);

        $transition = null;
        if (isset($this->transitions[$context][$name])) {
            $transition = $this->transitions[$context][$name];
        }

        if (! $transition) {
            $this->failureHandler->handleTransitionNotFound($name, $context);
        }

        return $transition;
    }

    /**
     * @param string $name
     * @param string $context
     *
     * @return bool
     */
    public function hasState($name, $context)
    {
        $this->assertStateNameIsValid($name);
        $this->assertContextNameIsValid($context);

        return isset($this->states[$context][$name]);
    }

    /**
     * @param string $name
     * @param string $context
     *
     * @return State
     */
    public function getState($name, $context)
    {
        $this->assertStateNameIsValid($name);
        $this->assertContextNameIsValid($context);
        if (! $this->hasState($name, $context)) {
            $this->failureHandler->handleStateNotFound($name, $context);
        }

        return $this->states[$context][$name];
    }

    /**
     * @param State $state
     * @param string $context
     */
    public function addState(State $state, $context)
    {
        $this->assertContextNameIsValid($context);
        $this->states[$context][$state->name()] = $state;
    }

    /**
     * @param string $context
     * @param string $state
     * @param StateAttribute $attribute
     */
    public function setAttribute($context, $state, StateAttribute $attribute)
    {
        $state = $this->getState($state, $context);
        $this->addState($state->addAttribute($attribute), $context);
    }

    /**
     * @param FailureHandler $handler
     */
    public function useFailureHandler(FailureHandler $handler)
    {
        $this->failureHandler = $handler;
    }

    /**
     * @param string $name
     */
    private function assertTransitionNameIsValid($name)
    {
        Assert::string($name, "The transition name must be a string value, '%s' given.");
    }

    /**
     * @param string $name
     */
    private function assertStateNameIsValid($name)
    {
        Assert::string($name, "The state name must be a string value, '%s' given.");
    }

    /**
     * @param $context
     */
    private function assertContextNameIsValid($context)
    {
        Assert::string($context, "The context must be a string value, '%s' given.");
    }
}
