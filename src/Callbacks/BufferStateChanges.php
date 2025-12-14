<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use Webmozart\Assert\Assert;
use function get_class;
use function is_object;

final class BufferStateChanges implements TransitionCallback
{
    /**
     * @var array<string, list<string>>
     */
    private array $buffer = [];

    /**
     * @param mixed|StateContext $context
     * @return string
     */
    private function extractContextIdentifier($context): string
    {
        if (! $context instanceof StateContext) {
            if (is_object($context)) {
                $context = get_class($context);
            }
        } else {
            $context = $context->toStateContextIdentifier();
        }
        Assert::string($context, 'Context is expected to be a string. Got: %s');

        return $context;
    }

    public function beforeStateChange(
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
    ): void {
        $this->buffer[$this->extractContextIdentifier($context)][] = __FUNCTION__;
    }

    public function afterStateChange(
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
    ): void {
        $this->buffer[$this->extractContextIdentifier($context)][] = __FUNCTION__;
    }

    public function onFailure(
        InvalidStateTransitionException $exception,
        /* StateContext in 4.0 */ $context,
        StateMachine $machine
    ): string {
        $this->buffer[$this->extractContextIdentifier($context)][] = get_class($exception);

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
