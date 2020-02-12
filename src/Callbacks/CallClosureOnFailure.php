<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class CallClosureOnFailure implements TransitionCallback
{
    /**
     * @var \Closure
     */
    private $callback;

    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
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
     * @param mixed $context
     * @param StateMachine $machine
     *
     * @return string
     */
    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine): string
    {
        $callback = $this->callback;

        return $callback($context);
    }
}
