<?php

namespace Star\Component\State\Handlers;

use Star\Component\State\FailureHandler;
use Star\Component\State\StateContext;

final class NullHandler implements FailureHandler
{
    /**
     * @param string $transition
     * @param StateContext $context
     * @param string $current
     */
    public function beforeTransitionNotAllowed($transition, StateContext $context, $current)
    {
        // do nothing
    }
}
