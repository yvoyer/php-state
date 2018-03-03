<?php

namespace Star\Component\State;

interface RegistryBuilder
{
    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerStartingState($transition, $stateName, array $attributes);

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerDestinationState($transition, $stateName, array $attributes);
}
