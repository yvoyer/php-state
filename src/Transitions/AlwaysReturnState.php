<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;
use Star\Component\State\StateMetadata;
use Webmozart\Assert\Assert;

final class AlwaysReturnState implements TransitionCallback
{
    /**
     * @var StateMetadata
     */
    private $to;

    /**
     * @param string $to
     */
    public function __construct($to)
    {
        Assert::string($to);
        $this->to = $to;
    }

    /**
     * @param InvalidStateTransitionException $exception
     * @param StateMachine $machine
     *
     * @return string The new state to move to on failure
     */
    public function onFailure(InvalidStateTransitionException $exception, StateMachine $machine)
    {
        return $this->to;
    }
}
