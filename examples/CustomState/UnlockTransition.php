<?php

namespace Star\Component\State\Example\CustomState;

use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;
use Star\Component\State\StateTransition;
use Star\Component\State\TransitionRegistry;

final class UnlockTransition implements StateTransition
{
    /**
     * @return string
     */
    public function getName()
    {
        return DoorState::UNLOCK;
    }

    /**
     * @param StateMachine $machine
     *
     * @return bool
     */
    public function isAllowed(StateMachine $machine)
    {
        return $machine->isInState(DoorState::LOCKED);
    }

    /**
     * @param TransitionRegistry $registry
     */
    public function onRegister(TransitionRegistry $registry)
    {
        $registry->addState(new LockedDoor());
        $registry->addState(new UnlockedDoor());
    }

    /**
     * @param StateContext $context
     */
    public function beforeStateChange(StateContext $context)
    {
    }

    /**
     * @param StateContext $context
     * @param StateMachine $machine
     */
    public function onStateChange(StateContext $context, StateMachine $machine)
    {
        $machine->setCurrentState(new UnlockedDoor());
    }

    /**
     * @param StateContext $context
     */
    public function afterStateChange(StateContext $context)
    {
    }
}
