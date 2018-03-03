<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateTransition;
use Webmozart\Assert\Assert;

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

    /**
     * @param string $name
     * @param string $from
     * @param string $to
     */
    public function __construct($name, $from, $to)
    {
        Assert::string($name);
        Assert::string($from);
        Assert::string($to);
        $this->name = $name;
        $this->from = $from;
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
        $registry->registerStartingState($this->name, $this->from, []);
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
