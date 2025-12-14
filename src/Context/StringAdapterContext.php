<?php declare(strict_types=1);

namespace Star\Component\State\Context;

use Star\Component\State\StateContext;

/**
 * Adapter for string context.
 */
final readonly class StringAdapterContext implements StateContext
{
    public function __construct(
        private string $context,
    ) {
    }

    public function toStateContextIdentifier(): string
    {
        return $this->context;
    }
}
