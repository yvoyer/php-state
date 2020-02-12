<?php declare(strict_types=1);

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateTransition;
use Webmozart\Assert\Assert;

final class ManyToOneTransition implements StateTransition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string[]
     */
    private $fromStates;

    /**
     * @var string
     */
    private $to;

    public function __construct(string $name, string $to, string ...$fromStates)
    {
        $this->name = $name;
        Assert::greaterThanEq(\count($fromStates), 1, 'Expected at least %2$s state. Got: %s');
        $this->fromStates = $fromStates;
        $this->to = $to;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function onRegister(RegistryBuilder $registry): void
    {
        foreach ($this->fromStates as $from) {
            $registry->registerStartingState($this->name, $from, []);
        }

        $registry->registerDestinationState($this->name, $this->to, []);
    }

    public function getDestinationState(): string
    {
        return $this->to;
    }
}
