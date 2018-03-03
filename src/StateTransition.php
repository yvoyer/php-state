<?php
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
    public function onRegister(RegistryBuilder $registry);

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getDestinationState();
}
