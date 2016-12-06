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
     * @param string $state
     * @param string $contextAlias
     *
     * @return NotFoundException
     */
    public static function stateNotFound($state, $contextAlias)
    {
        return new self(
            sprintf("The state '%s' could not be found for context '%s'.", $state, $contextAlias)
        );
    }

    /**
     * @param string $name
     * @param string $contextAlias
     *
     * @return NotFoundException
     */
    public static function transitionNotFound($name, $contextAlias)
    {
        return new self(
            sprintf("The transition '%s' could not be found for context '%s'.", $name, $contextAlias)
        );
    }
}
