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
    public function getName();

    /**
     * @param string $from
     *
     * @return bool
     */
    public function isAllowed($from);

    /**
     * @param StateRegistry $registry
     */
    public function onRegister(StateRegistry $registry);

    /**
     * @param StateContext $context
     */
    public function beforeStateChange(StateContext $context);

    /**
     * @param StateContext $context
     * @param StateMachine $machine
     */
    public function onStateChange(StateContext $context, StateMachine $machine);

    /**
     * @param StateContext $context
     */
    public function afterStateChange(StateContext $context);

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor);
}
