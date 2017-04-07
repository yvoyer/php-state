<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class NotFoundException extends \Exception
{
    /**
     * @param string $name
     *
     * @return NotFoundException
     */
    public static function stateNotFound($name)
    {
        return new self(sprintf("The state '%s' could not be found.", $name));
    }

    /**
     * @param string $name
     *
     * @return NotFoundException
     */
    public static function transitionNotFound($name)
    {
        return new self(sprintf("The transition '%s' could not be found.", $name));
    }
}
