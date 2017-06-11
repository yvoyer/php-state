<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\States\CustomStateBuilder;
use Star\Component\State\Builder\AttributeBuilder;
use Star\Component\State\Builder\TransitionBuilder;

final class DoorCustomState implements CustomStateBuilder
{
    /**
     * Register your custom states.
     *
     * @param TransitionBuilder $builder
     */
    public function registerTransitions(TransitionBuilder $builder)
    {
        $builder->allowTransition('lock', 'unlocked', 'locked');
        $builder->allowTransition('unlock', 'locked', 'unlocked');
    }

    /**
     * @param AttributeBuilder $builder
     */
    public function registerAttributes(AttributeBuilder $builder)
    {
        $builder->addAttribute('handle_is_turnable', ['locked', 'unlocked']);
    }
}
