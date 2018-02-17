<?php

namespace Star\Component\State\Visitor;

use Star\Component\State\StateVisitor;

final class AttributeDumper implements StateVisitor
{
    /**
     * An array having the state name as key and the attributes of this state.
     * @var array
     */
    private $structure = [];

    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param string $name
     * @param array $attributes
     */
    public function visitState($name, array $attributes)
    {
        if (! isset($this->structure[$name])) {
            $this->structure[$name] = [];
        }

        $this->structure[$name] = array_unique(array_merge($this->structure[$name], $attributes));
    }
}
