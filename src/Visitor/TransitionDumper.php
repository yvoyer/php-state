<?php

namespace Star\Component\State\Visitor;

use Star\Component\State\TransitionVisitor;
use Webmozart\Assert\Assert;

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

    public function visitFromState($state, array $attributes)
    {
        Assert::string($state);
        $this->structure[$this->currentTransition]['from'][] = $state;
    }

    public function visitToState($state, array $attributes)
    {
        $this->structure[$this->currentTransition]['to'] = $state;
    }
}
