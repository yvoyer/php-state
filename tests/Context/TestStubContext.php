<?php declare(strict_types=1);

namespace Star\Component\State\Context;

use Star\Component\State\StateContext;

final readonly class TestStubContext implements StateContext
{
    public function __construct(
        private string $identifier,
    ) {
    }

    public function toStateContextIdentifier(): string
    {
        return $this->identifier;
    }
}
