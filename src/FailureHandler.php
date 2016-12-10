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
     */
    public function handleTransitionNotAllowed(StateContext $context, StateTransition $transition);

    /**
     * Launched when a no transition are found for the context and state.
     *
     * @param string $name
     * @param string $context
     */
    public function handleStateNotFound($name, $context);

    /**
     * @param string $name The transition name
     * @param string $context The context alias
     */
    public function handleTransitionNotFound($name, $context);
}
