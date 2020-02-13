<?php declare(strict_types=1);

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateTransition;

final class OneToOneTransition implements StateTransition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    public function __construct(string $name, string $from, string $to)
    {
        $this->name = $name;
        $this->from = $from;
        $this->to = $to;
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
