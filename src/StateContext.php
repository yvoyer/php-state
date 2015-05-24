<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

/**
 * Class StateContext
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State
 */
interface StateContext
{
    /**
     * This method SHOULD only be called by the State implementation.
     *
     * @param State $state
     */
    public function setState(State $state);
}
