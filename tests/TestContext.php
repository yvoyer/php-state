<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

/**
 * @deprecated Will be removed in 4.0. You should use StateContext.
 * @see StateContext
 */
final class TestContext implements StateContext
{
    public function toStateContextIdentifier(): string
    {
        return 'context';
    }
}
