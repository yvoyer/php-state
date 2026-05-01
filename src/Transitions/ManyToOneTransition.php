<?php declare(strict_types=1);

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateTransition;
use Webmozart\Assert\Assert;
use function count;

final readonly class ManyToOneTransition implements StateTransition
{
    /**
     * @var string[]
     */
    private array $fromStates;

    public function __construct(
        private string $name,
        private string $to,
        string ...$fromStates,
    ) {
        $this->fromStates = $fromStates;
        Assert::greaterThanEq(count($this->fromStates), 1, 'Expected at least %2$s state. Got: %s');
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
