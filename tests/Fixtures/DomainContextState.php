<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Fixtures;

use Star\Component\State\LogicException;
use Star\Component\State\State;

/**
 * @author  Yannick Voyer (http://github.com/yvoyer)
 */
abstract class DomainContextState implements State
{
    /**
     * @param DomainContext $context
     * @throws \LogicException
     */
    public function enable(DomainContext $context)
    {
        throw LogicException::createInvalidTransition($this->stateValue(), 'enabled');
    }

    /**
     * @param DomainContext $context
     * @throws \LogicException
     */
    public function disable(DomainContext $context)
    {
        throw LogicException::createInvalidTransition($this->stateValue(), 'disabled');
    }

    /**
     * @param DomainContext $context
     * @throws \LogicException
     */
    public function suspend(DomainContext $context)
    {
        throw LogicException::createInvalidTransition($this->stateValue(), 'suspended');
    }

    public function isEnabled()
    {
        return false;
    }

    public function isDisabled()
    {
        return false;
    }

    public function isSuspended()
    {
        return false;
    }
}
