<?php declare(strict_types=1);

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateTransition;

final readonly class OneToOneTransition implements StateTransition
{
    public function __construct(
        private string $name,
        private string $from,
        private string $to,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function onRegister(RegistryBuilder $registry): void
    {
        $registry->registerStartingState($this->name, $this->from, []);
        $registry->registerDestinationState($this->name, $this->to, []);
    }

    public function getDestinationState(): string
    {
        return $this->to;
    }
}
