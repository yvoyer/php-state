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
final class LogicException extends \Exception
{
    /**
     * @param string $from
     * @param string $to
     *
     * @return \LogicException
     */
    public static function createInvalidTransition($from, $to)
    {
        return new self("The state cannot be {$to} when {$from}.");
    }
}
