<?php

namespace Star\Component\State\Transitions;

use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class CallMethodOnContext implements TransitionCallback
{
    /**
     * @var string
     */
    private $to;

    /**
     * @var object
     */
    private $context;

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
     * @param object $context
     * @param string $method
     * @param array $args
     */
    public function __construct(
        $to,
        $context,
        $method,
        array $args
    ) {
        $this->to = $to;
        $this->context = $context;
        $this->method = $method;
        $this->args = $args;
    }

    /**
     * @param InvalidStateTransitionException $exception
     * @param StateMachine $machine
     *
     * @return string The new state to move to on failure
     */
    public function onFailure(InvalidStateTransitionException $exception, StateMachine $machine)
    {
        call_user_func_array([$this->context, $this->method], $this->args);

        return $this->to;
    }
}
