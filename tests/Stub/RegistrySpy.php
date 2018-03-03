<?php

namespace Star\Component\State\Stub;

use Star\Component\State\RegistryBuilder;

final class RegistrySpy implements RegistryBuilder
{
    public $states = [];

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerStartingState($transition, $stateName, array $attributes)
    {
        $this->states[$transition]['start'][][$stateName] = $attributes;
    }

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerDestinationState($transition, $stateName, array $attributes)
    {
        $this->states[$transition]['destination'][][$stateName] = $attributes;
    }
}
