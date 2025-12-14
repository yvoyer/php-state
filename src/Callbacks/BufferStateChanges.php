<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use RuntimeException;
use Star\Component\State\InvalidStateTransitionException;
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

    public function beforeStateChange($context, StateMachine $machine): void
    {
        if (is_object($context)) {
            $context = get_class($context);
        }
        Assert::string($context, 'Context is expected to be a string. Got: %s');

        $this->buffer[$context][] = __FUNCTION__;
    }

    public function afterStateChange($context, StateMachine $machine): void
    {
        if (is_object($context)) {
            $context = get_class($context);
        }
        Assert::string($context, 'Context is expected to be a string. Got: %s');

        $this->buffer[$context][] = __FUNCTION__;
    }

    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine): string
    {
        throw new RuntimeException(__METHOD__ . ' is not implemented yet.');
    }

    /**
     * @return array<string, list<string>>
     */
    public function flushBuffer(): array
    {
        return $this->buffer;
    }
}
