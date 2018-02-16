<?php

namespace Star\Component\State;

interface StateRegistry
{
    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function registerState($name, array $attributes);

    /**
     * @param string $name The transition name
     *
     * @return StateTransition
     * @throws NotFoundException
     */
    public function getTransition($name);

    /**
     * @param string $name
     * @return State
     * @throws NotFoundException
     */
    public function getState($name);

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor);

    /**
     * @param StateVisitor $visitor
     */
    public function acceptStateVisitor(StateVisitor $visitor);
}
