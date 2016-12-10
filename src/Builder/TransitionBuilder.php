<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Builder;

use Star\Component\State\StateTransition;
use Star\Component\State\OneToOneTransition;
use Star\Component\State\StringState;

final class TransitionBuilder
{
    /**
     * @param string $name
     * @param string $from
     * @param string $to
     *
     * @return StateTransition
     */
    public function createTransition($name, $from, $to)
    {
        return new OneToOneTransition($name, new StringState($from), new StringState($to));
    }
}
