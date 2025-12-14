<?php declare(strict_types=1);

namespace Star\Component\State\Event\Adapter;

use Star\Component\State\StateContext;
use function get_class;
use function sprintf;
use function strrpos;
use function substr;
use function trigger_error;

/**
 * Adapter for object that do not yet implement the interface.
 * @deprecated Will be removed in 4.0. Adapter during the transition to 4.0.
 */
final class ObjectAdapterContext implements StateContext
{
    /**
     * @var object
     */
    private $object;

    public function __construct(
        object $object,
        bool $logError = true // deprecated: will be removed in 4.0
    ) {
        $this->object = $object;

        if ($logError) {
            @trigger_error(
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
