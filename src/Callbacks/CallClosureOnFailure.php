<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Closure;
use InvalidArgumentException;
use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;
use function is_string;
use function sprintf;

final class CallClosureOnFailure implements TransitionCallback
{
    private Closure $callback;

    public function __construct(Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param mixed $context
     * @param StateMachine $machine
     */
    public function beforeStateChange(
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
    ): void {
    }

    /**
     * @param mixed $context
     * @param StateMachine $machine
     */
    public function afterStateChange(
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
    ): void {
    }

    /**
     * @param InvalidStateTransitionException $exception
     * @param mixed $context
     * @param StateMachine $machine
     *
     * @return string
     */
    public function onFailure(
        InvalidStateTransitionException $exception,
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
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
