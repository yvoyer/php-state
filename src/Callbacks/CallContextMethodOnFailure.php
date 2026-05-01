<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

final readonly class CallContextMethodOnFailure implements TransitionCallback
{
    /**
     * @param mixed[] $args
     */
    public function __construct(
        private string $to,
        private string $method,
        private array $args
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
        $closure = function (array $args) use ($context) {
            $context->{$this->method}(...$args);
        };
        $closure($this->args);

        return $this->to;
    }
}
