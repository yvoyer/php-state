<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

interface StateTransition
{
    /**
     * @return string
     */
    public function name();

    /**
     * @param StateContext $context
     *
     * @return bool
     */
    public function changeIsRequired(StateContext $context);

    /**
     * @param StateMachine $machine
     * @param StateContext $context
     *
     * @return bool
     */
    public function isAllowed(StateMachine $machine, StateContext $context);

	/**
	 * @param TransitionRegistry $registry
	 */
	public function onRegister(TransitionRegistry $registry);

	/**
	 * @param StateContext $context
	 */
	public function beforeStateChange(StateContext $context);

	/**
	 * @param StateContext $context
	 */
	public function onStateChange(StateContext $context);

	/**
	 * @param StateContext $context
	 */
	public function afterStateChange(StateContext $context);
}
