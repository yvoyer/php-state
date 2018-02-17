<?php

namespace Star\Component\State;

interface RegistryBuilder
{
    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function registerState($name, array $attributes);
}
