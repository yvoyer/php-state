<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

final class AlwaysThrowExceptionOnFailure implements TransitionCallback
{
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

    /**
     * @throws InvalidStateTransitionException
     */
    public function onFailure(
        InvalidStateTransitionException $exception,
        StateContext $context,
        StateMachine $machine,
    ): string {
        throw $exception;
    }
}
