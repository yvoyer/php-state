<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use RuntimeException;
use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

final class NullCallback implements TransitionCallback
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

    public function onFailure(
        InvalidStateTransitionException $exception,
        StateContext $context,
        StateMachine $machine,
    ): string {
        throw new RuntimeException('Method ' . __METHOD__ . ' should never be called.');
    }
}
