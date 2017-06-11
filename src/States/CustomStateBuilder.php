<?php

namespace Star\Component\State\States;

use Star\Component\State\Builder\AttributeBuilder;
use Star\Component\State\Builder\TransitionBuilder;

interface CustomStateBuilder
{
    /**
     * @param TransitionBuilder $builder
     */
    public function registerTransitions(TransitionBuilder $builder);

    /**
     * @param AttributeBuilder $builder
     */
    public function registerAttributes(AttributeBuilder $builder);
}
