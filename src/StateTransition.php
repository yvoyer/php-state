<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

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
