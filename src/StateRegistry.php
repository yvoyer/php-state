<?php

namespace Star\Component\State;

interface StateRegistry
{
    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function registerState($name, array $attributes);
}
