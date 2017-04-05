<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Attribute\StateAttribute;

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
     * @param StateTransition $transition
     */
    public function addTransition(StateTransition $transition)
    {
	    if (isset($this->transitions[$transition->name()])) {
		    $this->failureHandler->onTransitionAlreadyRegistered($transition->name());
	    }

        $this->transitions[$transition->name()] = $transition;
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
        $transition = null;
        if (isset($this->transitions[$name])) {
            $transition = $this->transitions[$name];
        }

        if (! $transition) {
            $this->failureHandler->handleTransitionNotFound($name);
        }

        return $transition;
    }

    /**
     * @param string $name
     *
     * @return State
     */
    public function getState($name)
    {
        if (! isset($this->states[$name])) {
            $this->failureHandler->handleStateNotFound($name);
        }

        return $this->states[$name];
    }

    /**
     * @param State $state
     */
    public function addState(State $state)
    {
	    if (! isset($this->states[$state->name()])) {
		    $this->states[$state->name()] = $state;
	    }

	    if (! $state->matchState($this->getState($state->name()))) {
		    $this->failureHandler->onStateAlreadyRegistered($state->name());
	    }
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
}
