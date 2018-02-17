<?php

namespace Star\Component\State\Handlers;

use Star\Component\State\FailureHandler;
use Star\Component\State\StateContext;

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
     * @param string $transition
     * @param StateContext $context
     * @param string $current
     */
    public function beforeTransitionNotAllowed($transition, StateContext $context, $current)
    {
        call_user_func_array($this->closure, [$transition, $context, $current]);
    }
}
