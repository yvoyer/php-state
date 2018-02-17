<?php

namespace Star\Component\State\Handlers;

use Star\Component\State\FailureHandler;

final class NullHandler implements FailureHandler
{
    /**
     * @param string $transition
     * @param mixed $context
     * @param string $current
     */
    public function beforeTransitionNotAllowed($transition, $context, $current)
    {
        // do nothing
    }
}
