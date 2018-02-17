<?php

namespace Star\Component\State;

interface StateVisitor
{
    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function visitState($name, array $attributes);
}
