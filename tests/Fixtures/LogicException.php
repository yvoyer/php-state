<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Fixtures;

/**
 * Class LogicException
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State\Fixtures
 */
final class LogicException
{
    /**
     * @param string $from
     * @param string $to
     *
     * @return \LogicException
     */
    public static function createInvalidTransition($from, $to)
    {
        return new \LogicException("The state cannot be {$to} when {$from}.");
    }
}
