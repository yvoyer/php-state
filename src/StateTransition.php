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
     * @param string $from
     *
     * @return bool
     */
    public function isAllowed($from);

    /**
     * @param RegistryBuilder $registry
     */
    public function onRegister(RegistryBuilder $registry);

    /**
     * @param mixed $context
     */
    public function beforeStateChange($context);

    /**
     * @param StateMachine $machine
     */
    public function onStateChange(StateMachine $machine);

    /**
     * @param mixed $context
     */
    public function afterStateChange($context);

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor);

    /**
     * @param StateVisitor $visitor
     * @param StateRegistry $registry
     */
    public function acceptStateVisitor(StateVisitor $visitor, StateRegistry $registry);
}
