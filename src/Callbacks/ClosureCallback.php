<?php

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;
use Webmozart\Assert\Assert;

final class ClosureCallback implements TransitionCallback
{
    /**
     * @var \Closure
     */
    private $callback;

    /**
     * @param \Closure $callback
     */
    public function __construct(\Closure $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @param mixed $context
     * @param StateMachine $machine
     */
    public function beforeStateChange($context, StateMachine $machine)
    {
    }

    /**
     * @param mixed $context
     * @param StateMachine $machine
     */
    public function afterStateChange($context, StateMachine $machine)
    {
    }

    /**
     * @param InvalidStateTransitionException $exception
     * @param mixed $context
     * @param StateMachine $machine
     *
     * @return string
     */
    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine)
    {
        $callback = $this->callback;
        /**
         * @var string $state
         */
        $state = $callback($context);
        Assert::string($state);

        return $state;
    }
}
