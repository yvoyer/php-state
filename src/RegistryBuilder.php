<?php declare(strict_types=1);

namespace Star\Component\State;

interface RegistryBuilder
{
    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerStartingState(string $transition, string $stateName, array $attributes): void;

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerDestinationState(string $transition, string $stateName, array $attributes): void;
}
