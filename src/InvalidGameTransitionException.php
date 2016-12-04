<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class InvalidGameTransitionException extends \Exception
{
    /**
     * @param State $from
     * @param State $to
     *
     * @return InvalidGameTransitionException
     */
    public static function invalidTransition(State $from, State $to)
    {
        return new self("The transition from '{$from->toString()}' to '{$to->toString()}' is not allowed.");
    }
}
