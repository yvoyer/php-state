<?php

namespace Star\Component\State\Visitor;

use Star\Component\State\State;
use Star\Component\State\TransitionVisitor;

final class TransitionDumper implements TransitionVisitor
{
    /**
     * @var array
     */
    private $structure = [];

    /**
     * @var string
     */
    private $currentTransition;

    /**
     * @return array
     */
    public function getStructure()
    {
        return $this->structure;
    }

    public function visitTransition($name)
    {
        $this->currentTransition = $name;
    }

    public function visitFromState(State $state)
    {
        $this->structure[$this->currentTransition]['from'][] = $state->getName();
    }

    public function visitToState(State $state)
    {
        $this->structure[$this->currentTransition]['to'][] = $state->getName();
    }
}
