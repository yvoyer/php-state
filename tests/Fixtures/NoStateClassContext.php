<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Fixtures;

use Star\Component\State\State;
use Star\Component\State\StateContext;

/**
 * Class NoStateClassContext
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State\Fixtures
 */
final class NoStateClassContext implements StateContext
{
    const STATE_DISABLED = 0;
    const STATE_ENABLED = 1;
    const STATE_SUSPENDED = 2;

    private $state = 0;

    public function enable()
    {
        if ($this->isEnabled()) {
            throw LogicException::createInvalidTransition('enabled', 'enabled');
        }

        $this->state = self::STATE_ENABLED;
    }

    public function disable()
    {
        if ($this->isDisabled()) {
            throw LogicException::createInvalidTransition('disabled', 'disabled');
        }

        if ($this->isSuspended()) {
            throw LogicException::createInvalidTransition('suspended', 'disabled');
        }

        $this->state = self::STATE_DISABLED;
    }

    public function suspend()
    {
        if ($this->isDisabled()) {
            throw LogicException::createInvalidTransition('disabled', 'suspended');
        }

        if ($this->isSuspended()) {
            throw LogicException::createInvalidTransition('suspended', 'suspended');
        }

        $this->state = self::STATE_SUSPENDED;
    }

    public function isEnabled()
    {
        return $this->state == self::STATE_ENABLED;
    }

    public function isDisabled()
    {
        return $this->state == self::STATE_DISABLED;
    }

    public function isSuspended()
    {
        return $this->state == self::STATE_SUSPENDED;
    }

    /**
     * This method SHOULD only be called by the State implementation.
     *
     * @param State $state
     */
    public function setState(State $state)
    {
        throw new \RuntimeException(__METHOD__ . ' Not implemented yet');
    }
}
