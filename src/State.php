<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

interface State
{
    /**
     * The string value of the state
     *
     * @return string
     */
    public function getName();

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute);

    /**
     * @param string $attribute
     */
    public function addAttribute($attribute);

    /**
     * @param string[] $attributes
     */
    public function addAttributes(array $attributes);

    /**
     * @param StateRegistry $registry
     */
    public function register(StateRegistry $registry);

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitorFrom(TransitionVisitor $visitor);

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitorTo(TransitionVisitor $visitor);
}
