<?php

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

    /**
     * @param string $name
     * @param string[] $fromStates
     * @param string $to
     */
    public function __construct($name, array $fromStates, $to)
    {
        Assert::string($name);
        $this->name = $name;
        Assert::greaterThanEq(count($fromStates), 1, 'Expected at least %2$s state. Got: %s');
        Assert::allString($fromStates);
        Assert::string($to);
        $this->fromStates = $fromStates;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param RegistryBuilder $registry
     */
    public function onRegister(RegistryBuilder $registry)
    {
        foreach ($this->fromStates as $from) {
            $registry->registerStartingState($this->name, $from, []);
        }

        $registry->registerDestinationState($this->name, $this->to, []);
    }

    /**
     * @return string
     */
    public function getDestinationState()
    {
        return $this->to;
    }
}
