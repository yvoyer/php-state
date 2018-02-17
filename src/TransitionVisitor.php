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
     */
    public function visitFromState($state);

    /**
     * @param string $state
     */
    public function visitToState($state);
}
