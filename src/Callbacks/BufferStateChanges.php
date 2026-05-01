<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use function get_class;

final class BufferStateChanges implements TransitionCallback
{
    /**
     * @var array<string, list<string>>
     */
    private array $buffer = [];

    public function beforeStateChange(
        StateContext $context,
        StateMachine $machine,
    ): void {
        $this->buffer[$context->toStateContextIdentifier()][] = __FUNCTION__;
    }

    public function afterStateChange(
        StateContext $context,
        StateMachine $machine,
    ): void {
        $this->buffer[$context->toStateContextIdentifier()][] = __FUNCTION__;
    }

    public function onFailure(
        InvalidStateTransitionException $exception,
        StateContext $context,
        StateMachine $machine,
    ): string {
        $this->buffer[$context->toStateContextIdentifier()][] = get_class($exception);

        return '';
    }

    /**
     * @return array<string, list<string>>
     */
    public function flushBuffer(): array
    {
        return $this->buffer;
    }
}
