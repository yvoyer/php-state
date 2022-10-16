<?php declare(strict_types=1);

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateTransition;

final class ReadOnlyTransition implements StateTransition
{
    private string $destination;

    public function __construct(string $destination)
    {
        $this->destination = $destination;
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
