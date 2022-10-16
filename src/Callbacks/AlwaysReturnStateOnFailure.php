<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class AlwaysReturnStateOnFailure implements TransitionCallback
{
    private string $to;

    public function __construct(string $to)
    {
        $this->to = $to;
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
        return $this->to;
    }
}
