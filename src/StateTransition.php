<?php declare(strict_types=1);

namespace Star\Component\State;

interface StateTransition
{
    /**
     * @param RegistryBuilder $registry
     */
    public function onRegister(RegistryBuilder $registry): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string
     */
    public function getDestinationState(): string;
}
