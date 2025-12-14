<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use RuntimeException;
use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class BufferStateChanges implements TransitionCallback
{
    /**
     * @var array
     */
    private array $buffer = [];

    public function beforeStateChange($context, StateMachine $machine): void
    {
        $this->buffer[$context][] = __FUNCTION__;
    }

    public function afterStateChange($context, StateMachine $machine): void
    {
        $this->buffer[$context][] = __FUNCTION__;
    }

    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine): string
    {
        throw new RuntimeException(__METHOD__ . ' is not implemented yet.');
    }

    public function flushBuffer(): array
    {
        return $this->buffer;
    }
}
