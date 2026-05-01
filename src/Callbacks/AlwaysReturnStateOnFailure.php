<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

final readonly class AlwaysReturnStateOnFailure implements TransitionCallback
{
    public function __construct(
        private string $to,
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
        return $this->to;
    }
}
