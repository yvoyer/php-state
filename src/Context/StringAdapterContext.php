<?php declare(strict_types=1);

namespace Star\Component\State\Context;

use Star\Component\State\StateContext;
use function sprintf;
use function trigger_error;

/**
 * Adapter for string context.
 */
final class StringAdapterContext implements StateContext
{
    /**
     * @var string
     */
    private $context;

    public function __construct(
        string $context,
        bool $triggerError = false // deprecated: will be removed in 4.0
    ) {
        $this->context = $context;
        if ($triggerError) {
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
