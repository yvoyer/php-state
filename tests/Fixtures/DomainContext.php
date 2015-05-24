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
 * Class DomainContext
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State\Fixtures
 */
final class DomainContext implements StateContext
{
    /**
     * @var DomainContextState
     */
    private $state;

    public function __construct()
    {
        $this->state = new Disable();
    }

    public function enable()
    {
        $this->state->enable($this);
    }

    public function disable()
    {
        $this->state->disable($this);
    }

    public function suspend()
    {
        $this->state->suspend($this);
    }

    public function isEnabled()
    {
        return $this->state->isEnabled();
    }

    public function isDisabled()
    {
        return $this->state->isDisabled();
    }

    public function isSuspended()
    {
        return $this->state->isSuspended();
    }

    /**
     * This method SHOULD only be called by the State implementation.
     *
     * @param State $state
     */
    public function setState(State $state)
    {
        $this->state = $state;
    }
}
