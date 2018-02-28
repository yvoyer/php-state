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
     * Todo remove in favor of TransitionCallback
     */
    public function beforeStateChange($context);

    /**
     * @return string
     */
    public function getDestinationState();

    /**
     * @param mixed $context
     * Todo remove in favor of TransitionCallback
     */
    public function afterStateChange($context);

    /**
     * @param TransitionVisitor $visitor
     * todo Find a way to remove from interface (new interface maybe)
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor);

    /**
     * @param StateVisitor $visitor
     * @param StateRegistry $registry
     * todo Find a way to remove from interface (new interface maybe)
     */
    public function acceptStateVisitor(StateVisitor $visitor, StateRegistry $registry);
}
