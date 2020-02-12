<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class CallContextMethodOnFailure implements TransitionCallback
{
    /**
     * @var string
     */
    private $to;

    /**
     * @var string
     */
    private $method;

    /**
     * @var mixed[]
     */
    private $args;

    /**
     * @param string $to
     * @param string $method
     * @param mixed[] $args
     */
    public function __construct(
        string $to,
        string $method,
        array $args
    ) {
        $this->to = $to;
        $this->method = $method;
        $this->args = $args;
    }

    /**
     * @param mixed $context
     * @param StateMachine $machine
     */
    public function beforeStateChange($context, StateMachine $machine): void
    {
    }

    /**
     * @param mixed $context
     * @param StateMachine $machine
     */
    public function afterStateChange($context, StateMachine $machine): void
    {
    }

    /**
     * @param InvalidStateTransitionException $exception
     * @param mixed|object $context
     * @param StateMachine $machine
     *
     * @return string
     */
    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine): string
    {
        $closure = function (array $args) use ($context) {
            $context->{$this->method}(...$args);
        };
        $closure($this->args);

        return $this->to;
    }
}
