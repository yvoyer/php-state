<?php declare(strict_types=1);

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
