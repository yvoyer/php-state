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
     * @var string
     */
    private $currentState;

    /**
     * @param string $currentState
     * @param StateRegistry|null $registry
     */
    public function __construct($currentState, StateRegistry $registry = null)
    {
        Assert::string($currentState);
        if (! $registry) {
            $registry = new TransitionRegistry();
        }

        $this->dispatcher = new EventDispatcher();
        $this->registry = $registry;
        $this->setCurrentState($currentState);
    }

    /**
     * @param string $transitionName The transition name
     * @param mixed $context
     * @param FailureHandler $handler Gives you the possibility to perform some task when transition not allowed
     *
     * @return string The next state to store on your context
     * @throws InvalidStateTransitionException
     * @throws NotFoundException
     */
    public function transit($transitionName, $context, FailureHandler $handler = null)
    {
        Assert::string($transitionName);
        if (! $handler) {
            $handler = new NullHandler();
        }

        $transition = $this->registry->getTransition($transitionName);

        if (! $transition->isAllowed($this->currentState)) {
            // todo dispatch exception event instead ?
            $handler->beforeTransitionNotAllowed($transitionName, $context, $this->currentState);
            // always throw exception when not allowed
            throw InvalidStateTransitionException::notAllowedTransition($transitionName, $context, $this->currentState);
        }

        $this->dispatcher->dispatch(
            StateEventStore::BEFORE_TRANSITION,
            new TransitionWasRequested($transitionName)
        );

        $transition->beforeStateChange($context);
        $transition->onStateChange($this);
        $transition->afterStateChange($context);

        $this->dispatcher->dispatch(
            StateEventStore::AFTER_TRANSITION,
            new TransitionWasSuccessful($transitionName)
        );

        return $this->currentState;
    }

    /**
     * @param string $stateName
     * @return bool
     * @throws NotFoundException
     */
    public function isInState($stateName)
    {
        Assert::string($stateName);
        if (! $this->registry->hasState($stateName)) {
            throw NotFoundException::stateNotFound($stateName);
        }

        return $this->currentState === $stateName;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        Assert::string($attribute);
        return $this->registry->getState($this->currentState)->hasAttribute($attribute);
    }

    /**
     * @param string $state
     * @internal Internal to the StateMachine service. You should not base your logic on this.
     */
    public function setCurrentState($state)
    {
        Assert::string($state);
        $this->currentState = $state;
    }

    /**
     * @param string $event
     * @param \Closure $listener
     */
    public function addListener($event, \Closure $listener)
    {
        Assert::string($event);
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
