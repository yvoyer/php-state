<?php

namespace Star\Component\State\Builder;

interface TransitionBuilder
{
    /**
     * @param string $name
     * @param string $from
     * @param string $to
     */
    public function allowTransition($name, $from, $to);
}
