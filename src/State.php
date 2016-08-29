<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

/**
 * @author  Yannick Voyer (http://github.com/yvoyer)
 */
interface State
{
    /**
     * The value representation of your state object for reconstruction or storage on your context.
     *
     * @return mixed
     */
    public function stateValue();
}
