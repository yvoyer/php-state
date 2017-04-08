<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class StateMachine
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var TransitionRegistry
     */
    private $registry;

    /**
     * @var State
     */
    private $currentState;

    /**
     * @param string $currentState
     * @param TransitionRegistry|null $registry
     */
    public function __construct($currentState, TransitionRegistry $registry = null)
    {
        if (! $registry) {
            $registry = new TransitionRegistry();
        }

        $this->dispatcher = new EventDispatcher();
        $this->registry = $registry;
        $this->setCurrentState($this->registry->getState($currentState));
    }

    /**
     * @param string $name The transition name
     * @param StateContext $context
     *
     * @return string The next state to store on your context
     * @throws InvalidStateTransitionException
     * @throws NotFoundException
     */
    public function transitContext($name, StateContext $context)
    {
        $transition = $this->registry->getTransition($name);

        if (! $transition->isAllowed($this)) {
            throw InvalidStateTransitionException::notAllowedTransition($transition, $context, $this->currentState);
        }

        $this->dispatcher->dispatch(
            StateEventStore::BEFORE_TRANSITION,
            new TransitionWasRequested($transition)
        );

        $transition->beforeStateChange($context);
        $transition->onStateChange($context, $this);
        $transition->afterStateChange($context);

        $this->dispatcher->dispatch(
            StateEventStore::AFTER_TRANSITION,
            new TransitionWasSuccessful($transition)
        );

        return $this->currentState->getName();
    }

    /**
     * @param string $stateName
     *
     * @return bool
     */
    public function isInState($stateName)
    {
        return $this->currentState->matchState($this->registry->getState($stateName));
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        return $this->currentState->hasAttribute($attribute);
    }

    /**
     * @param State $state
     * @internal Internal to the StateMachine service. You should not base your logic on this.
     */
    public function setCurrentState(State $state)
    {
        $this->currentState = $state;
    }

    /**
     * @param string $event
     * @param \Closure $listener
     */
    public function addListener($event, \Closure $listener)
    {
        $this->dispatcher->addListener($event, $listener);
    }
}
