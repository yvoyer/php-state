<?php declare(strict_types=1);

namespace Star\Component\State\Event\Adapter;

use Star\Component\State\StateContext;
use function error_log;
use function get_class;
use function sprintf;
use function strrpos;
use function substr;

/**
 * Adapter for object that do not yet implement the interface.
 * @deprecated Will be removed in 4.0. Adapter during the transition to 4.0.
 */
final readonly class ObjectAdapterContext implements StateContext
{
    public function __construct(
        private object $object,
    ) {
        @error_log(
            sprintf(
                'Passing an object of type "%s" that do not implement "%s" is deprecated. ' .
                'The object should implementing "%s" interface.',
                get_class($this->object),
                StateContext::class,
                StateContext::class,
            ),
            E_USER_DEPRECATED,
        );
    }

    public function toStateContextIdentifier(): string
    {
        $class = get_class($this->object);
        return substr($class, strrpos($class, '\\') + 1);
    }
}
