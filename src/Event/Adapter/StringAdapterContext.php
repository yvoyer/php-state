<?php declare(strict_types=1);

namespace Star\Component\State\Event\Adapter;

use Star\Component\State\StateContext;
use function trigger_error;
use function sprintf;

/**
 * Adapter for string context.
 * @deprecated Will be removed in 4.0. Adapter during the transition to 4.0.
 */
final class StringAdapterContext implements StateContext
{
    /**
     * @var string
     */
    private $context;

    public function __construct(
        string $context,
        bool $logError = true // deprecated: will be removed in 4.0
    ) {
        $this->context = $context;
        if ($logError) {
            @trigger_error(
                sprintf(
                    'Passing a string context "%s" is deprecated. ' .
                    'You should provide your own class implementing "%s" interface.',
                    $this->context,
                    StateContext::class,
                ),
                E_USER_DEPRECATED,
            );
        }
    }

    public function toStateContextIdentifier(): string
    {
        return $this->context;
    }
}
