<?php

namespace Star\Component\State\Transitions;

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
     * @param InvalidStateTransitionException $exception
     * @param StateMachine $machine
     *
     * @return string The new state to move to on failure
     */
    public function onFailure(InvalidStateTransitionException $exception, StateMachine $machine)
    {
        $callback = $this->callback;
        /**
         * @var string $state
         */
        $state = $callback();
        Assert::string($state);

        return $state;
    }
}
