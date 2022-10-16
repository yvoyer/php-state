<?php declare(strict_types=1);

namespace Star\Component\State\Stub;

use Star\Component\State\RegistryBuilder;

final class RegistrySpy implements RegistryBuilder
{
    /**
     * @var string[][][][]
     */
    public array $states = [];

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerStartingState(string $transition, string $stateName, array $attributes): void
    {
        $this->states[$transition]['start'][][$stateName] = $attributes;
    }

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerDestinationState(string $transition, string $stateName, array $attributes): void
    {
        $this->states[$transition]['destination'][][$stateName] = $attributes;
    }
}
