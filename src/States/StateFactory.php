<?php

namespace Star\Component\State\States;

use Star\Component\State\TransitionRegistry;

interface StateFactory
{
    /**
     * @param TransitionRegistry $registry
     */
    public function registerTransitions(TransitionRegistry $registry);
}
