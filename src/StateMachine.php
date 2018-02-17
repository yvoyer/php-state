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
use Star\Component\State\Handlers\NullHandler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Webmozart\Assert\Assert;

final class StateMachine
{
    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var StateRegistry
     */
    private $registry;

    /**
     * @var State
     */
    private $currentState;

    /**
     * @param string $currentState
     * @param StateRegistry|null $registry
     */
    public function __construct($currentState, StateRegistry $registry = null)
    {
        if (! $registry) {
            $registry = new TransitionRegistry();
        }

        $this->dispatcher = new EventDispatcher();
        $this->registry = $registry;
        $this->setCurrentState($currentState);
    }

    /**
     * @param string $transitionName The transition name
     * @param StateContext $context
     * @param FailureHandler $handler Gives you the possibility to perform some task when transition not allowed
     *
     * @return string The next state to store on your context
     * @throws InvalidStateTransitionException
     * @throws NotFoundException
     */
    public function transitContext($transitionName, StateContext $context, FailureHandler $handler = null)
    {
        if (! $handler) {
            $handler = new NullHandler();
        }

        $transition = $this->registry->getTransition($transitionName);

        if (! $transition->isAllowed($this->currentState->getName())) {
            $handler->beforeTransitionNotAllowed($transition, $context, $this->currentState);
            // always throw exception when not allowed
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
     * @param $transitionName
     * @param StateContext $context
     *
     * @return $this
     */
    public function transit($transitionName, StateContext $context) {
        // todo make sure its persistable
        $this->transitContext($transitionName, $context);

        return $this;
    }

    /**
     * @param string $stateName
     *
     * @return bool
     */
    public function isInState($stateName)
    {
        Assert::string($stateName);
        return $this->currentState->getName() === $stateName;
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
     * @param string $state
     * @internal Internal to the StateMachine service. You should not base your logic on this.
     */
    public function setCurrentState($state)
    {
        Assert::string($state);
        $this->currentState = $this->registry->getState($state);
    }

    /**
     * @param string $event
     * @param \Closure $listener
     */
    public function addListener($event, \Closure $listener)
    {
        $this->dispatcher->addListener($event, $listener);
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        $this->registry->acceptTransitionVisitor($visitor);
    }

    /**
     * @param StateVisitor $visitor
     */
    public function acceptStateVisitor(StateVisitor $visitor)
    {
        $this->registry->acceptStateVisitor($visitor);
    }
}
