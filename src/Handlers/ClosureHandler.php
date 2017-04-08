<?php

namespace Star\Component\State\Handlers;

use Star\Component\State\FailureHandler;
use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateTransition;

final class ClosureHandler implements FailureHandler
{
    /**
     * @var \Closure
     */
    private $closure;

    /**
     * @param \Closure $closure
     */
    public function __construct(\Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     * @param StateTransition $transition
     * @param StateContext $context
     * @param State $current
     */
    public function beforeTransitionNotAllowed(StateTransition $transition, StateContext $context, State $current)
    {
        call_user_func_array($this->closure, [$transition, $context, $current]);
    }
}
