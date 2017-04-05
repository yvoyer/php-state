<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

/**
 * Customize how the system will handle the failure points.
 */
interface FailureHandler
{
	/**
	 * Launched when a not allowed transition is detected.
	 *
	 * @param StateContext $context
	 * @param StateTransition $transition
	 * @param State $state
	 */
	public function handleTransitionNotAllowed(
		StateContext $context,
		StateTransition $transition,
		State $state
	);

    /**
     * Launched when a no transition are found for the context and state.
     *
     * @param string $name
     */
	public function handleStateNotFound($name);

    /**
     * @param string $name The transition name
     */
    public function handleTransitionNotFound($name);

	/**
	 * Launched when a state is already registered.
	 *
	 * @param string $name
	 */
	public function onStateAlreadyRegistered($name);

	/**
	 * Launched when a transition is already registered.
	 *
	 * @param string $name
	 */
	public function onTransitionAlreadyRegistered($name);
}
