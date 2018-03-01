<?php

namespace Star\Component\State\Callbacks;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;
use Webmozart\Assert\Assert;

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
     * @var array
     */
    private $args;

    /**
     * @param string $to
     * @param string $method
     * @param array $args
     */
    public function __construct(
        $to,
        $method,
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
        Assert::object($context);
        call_user_func_array([$context, $this->method], $this->args);

        return $this->to;
    }
}
