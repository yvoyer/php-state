<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Closure;
use InvalidArgumentException;
use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use function is_string;
use function sprintf;

final readonly class CallClosureOnFailure implements TransitionCallback
{
    public function __construct(
        private Closure $callback,
    ) {
    }

    public function beforeStateChange(
        StateContext $context,
        StateMachine $machine,
    ): void {
    }

    public function afterStateChange(
        StateContext $context,
        StateMachine $machine,
    ): void {
    }

    public function onFailure(
        InvalidStateTransitionException $exception,
        StateContext $context,
        StateMachine $machine,
    ): string {
        $callback = $this->callback;
        $return = $callback($context);
        if (!is_string($return)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Callback should be returning a string, type "%s" returned.',
                    gettype($return)
                ),
                E_USER_DEPRECATED
            );
        }

        return $return;
    }
}
