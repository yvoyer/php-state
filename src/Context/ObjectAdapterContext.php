<?php declare(strict_types=1);

namespace Star\Component\State\Context;

use Star\Component\State\StateContext;
use function get_class;
use function strrpos;
use function substr;

/**
 * Adapter for object that do not yet implement the interface.
 */
final readonly class ObjectAdapterContext implements StateContext
{
    public function __construct(
        private object $object,
    ) {
    }

    public function toStateContextIdentifier(): string
    {
        $class = get_class($this->object);
        $pos = strrpos($class, '\\');
        if ($pos > 0) {
            $pos = $pos + 1;
        }

        return substr($class, (int) $pos);
    }
}
