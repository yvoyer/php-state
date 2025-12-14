<?php declare(strict_types=1);

namespace Star\Component\State\Context;

use Star\Component\State\StateContext;

final class TestStubContext implements StateContext
{
    /**
     * @var string
     */
    private $identifier;

    public function __construct(
        string $identifier
    ) {
        $this->identifier = $identifier;
    }

    public function toStateContextIdentifier(): string
    {
        return $this->identifier;
    }
}
