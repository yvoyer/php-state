<?php

namespace Star\Component\State;

interface StateRegistry extends RegistryBuilder
{
    /**
     * @param string $name The transition name
     *
     * @return StateTransition
     * @throws NotFoundException
     */
    public function getTransition($name);

    /**
     * @param string $state
     * @param string $attribute
     */
    public function addAttribute($state, $attribute);

    /**
     * @param string $state
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($state, $attribute);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasState($name);

    /**
     * @param string $transition
     * @param string $state
     *
     * @return bool
     */
    public function transitionStartsFrom($transition, $state);

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor);

    /**
     * @param StateVisitor $visitor
     */
    public function acceptStateVisitor(StateVisitor $visitor);
}
