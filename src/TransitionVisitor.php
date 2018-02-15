<?php

namespace Star\Component\State;

interface TransitionVisitor
{
    /**
     * @param string $name
     */
    public function visitTransition($name);

    /**
     * @param State $state
     */
    public function visitFromState(State $state);

    /**
     * @param State $state
     */
    public function visitToState(State $state);
}
