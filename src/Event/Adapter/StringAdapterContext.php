<?php declare(strict_types=1);

namespace Star\Component\State\Event\Adapter;

use Star\Component\State\StateContext;
use function error_log;
use function sprintf;

/**
 * Adapter for string context.
 * @deprecated Will be removed in 4.0. Adapter during the transition to 4.0.
 */
final readonly class StringAdapterContext implements StateContext
{
    public function __construct(
        private string $context,
    ) {
        @error_log(
            sprintf(
                'Passing a string context "%s" is deprecated. ' .
                'You should provide your own class implementing "%s" interface.',
            $this->context,
                StateContext::class,
            ),
            E_USER_DEPRECATED,
        );
    }

    public function toStateContextIdentifier(): string
    {
        return $this->context;
    }
}
