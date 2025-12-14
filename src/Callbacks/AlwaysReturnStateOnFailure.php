<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

final class AlwaysReturnStateOnFailure implements TransitionCallback
{
    private string $to;

    public function __construct(string $to)
    {
        $this->to = $to;
    }

    public function beforeStateChange(
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
    ): void {
    }

    /**
     * @param string|object|StateContext $context
     * @param StateMachine $machine
     */
    public function afterStateChange(
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
    ): void {
    }

    /**
     * @param InvalidStateTransitionException $exception
     * @param string|object|StateContext $context
     * @param StateMachine $machine
     *
     * @return string
     */
    public function onFailure(
        InvalidStateTransitionException $exception,
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
    ): string {
        return $this->to;
    }
}
