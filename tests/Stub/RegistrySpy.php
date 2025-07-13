<?php declare(strict_types=1);

namespace Star\Component\State\Stub;

use Star\Component\State\RegistryBuilder;

final class RegistrySpy implements RegistryBuilder
{
    /**
     * @var array<string, array{
     *     start: array<int, array<string, string[]>>,
     *     destination: array<int, array<string, string[]>>,
     * }>
     */
    private array $states = [];

    /**
     * @param string $transition
     * @param string $type
     * @return array<int, array<string, string[]>>
     */
    public function getStates(string $transition, string $type): array
    {
        if (!isset($this->states[$transition][$type])) {
            return [];
        }

        return $this->states[$transition][$type];
    }

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerStartingState(string $transition, string $stateName, array $attributes): void
    {
        $this->states[$transition]['start'][][$stateName] = $attributes; // @phpstan-ignore-line
    }

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerDestinationState(string $transition, string $stateName, array $attributes): void
    {
        $this->states[$transition]['destination'][][$stateName] = $attributes; // @phpstan-ignore-line
    }
}
