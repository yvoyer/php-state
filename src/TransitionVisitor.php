<?php

namespace Star\Component\State;

interface TransitionVisitor
{
    /**
     * @param string $name
     */
    public function visitTransition($name);

    /**
     * @param string $state
     * @param string[] $attributes
     */
    public function visitFromState($state, array $attributes);

    /**
     * @param string $state
     * @param string[] $attributes
     */
    public function visitToState($state, array $attributes);
}
