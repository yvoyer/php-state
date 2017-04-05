<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class AlwaysThrowException implements FailureHandler
{
	/**
	 * Launched when a not allowed transition is detected.
	 *
	 * @param StateContext $context
	 * @param StateTransition $transition
	 * @param State $state
	 * @throws InvalidStateTransitionException
	 */
    public function handleTransitionNotAllowed(
	    StateContext $context,
	    StateTransition $transition,
	    State $state
    ) {
        throw InvalidStateTransitionException::notAllowedTransition($transition, $context, $state);
    }

    /**
     * Launched when a no transition are found for the context and state.
     *
	 * @param string $name
	 * @throws NotFoundException
	 */
    public function handleStateNotFound($name)
    {
        throw NotFoundException::stateNotFound($name);
    }

    /**
     * @param string $name The transition name
     * @throws NotFoundException
     */
    public function handleTransitionNotFound($name)
    {
	    throw NotFoundException::transitionNotFound($name);
    }

	/**
	 * Launched when a state is already registered
	 *
	 * @param string $name
	 */
	public function onStateAlreadyRegistered($name) {
		throw $this->generateException(sprintf("The state '%s' is already registered.", $name));
	}

	/**
	 * Launched when a transition is already registered.
	 *
	 * @param string $name
	 */
	public function onTransitionAlreadyRegistered($name) {
		throw $this->generateException(sprintf("The transition '%s' is already registered.", $name));
	}
}
