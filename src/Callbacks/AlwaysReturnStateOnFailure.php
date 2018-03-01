<?php

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;
use Webmozart\Assert\Assert;

final class AlwaysReturnStateOnFailure implements TransitionCallback
{
    /**
     * @var string
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
        return $this->to;
    }
}
