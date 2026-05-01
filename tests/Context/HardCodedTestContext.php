<?php declare(strict_types=1);

namespace Star\Component\State\Context;

use Star\Component\State\StateContext;

final class HardCodedTestContext implements StateContext
{
    public function toStateContextIdentifier(): string
    {
        return 'context';
    }
}
