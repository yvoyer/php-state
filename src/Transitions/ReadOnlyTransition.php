<?php declare(strict_types=1);

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateTransition;

final readonly class ReadOnlyTransition implements StateTransition
{
    public function __construct(
        private string $destination,
    ) {
    }

    public function getName(): string
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    public function onRegister(RegistryBuilder $registry): void
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    public function getDestinationState(): string
    {
        return $this->destination;
    }
}
