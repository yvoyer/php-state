<?php

namespace Star\Component\State;

interface StateVisitor
{
    /**
     * @param string $name
     * @param array $attributes
     */
    public function visitState($name, array $attributes);
}
