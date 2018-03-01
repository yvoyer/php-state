<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Callbacks\AlwaysThrowExceptionOnFailure;
use Star\Component\State\Callbacks\TransitionCallback;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasFailed;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
use Webmozart\Assert\Assert;

final class StateMachine
{
    /**
     * @var EventRegistry
     */
    private $listeners;

    /**
     * @var StateRegistry
     */
    private $states;

    /**
     * @var string
     */
    private $currentState;

    /**
     * @param string $currentState
     * @param StateRegistry $states
     * @param EventRegistry $listeners
     */
    public function __construct(
        $currentState,
        StateRegistry $states,
        EventRegistry $listeners
    ) {
        Assert::string($currentState);
        $this->listeners = $listeners;
        $this->states = $states;
        $this->setCurrentState($currentState);
    }

    /**
     * @param string $transitionName The transition name
     * @param mixed $context
     * @param TransitionCallback|null $callback
     *
     * @return string The next state to store on your context
     * @throws InvalidStateTransitionException
     * @throws NotFoundException
     */
    public function transit($transitionName, $context, TransitionCallback $callback = null)
    {
        if (! $callback) {
            $callback = new AlwaysThrowExceptionOnFailure();
        }

        $this->listeners->dispatch(
            StateEventStore::BEFORE_TRANSITION,
            new TransitionWasRequested($transitionName)
        );

        Assert::string($transitionName);
        $transition = $this->states->getTransition($transitionName);
        $callback->beforeStateChange($context, $this);

        $newState = $transition->getDestinationState();
        if (! $transition->isAllowed($this->currentState)) {
            $exception = InvalidStateTransitionException::notAllowedTransition(
                $transitionName,
                $context,
                $this->currentState
            );

            $this->listeners->dispatch(
                StateEventStore::FAILURE_TRANSITION,
                new TransitionWasFailed($transitionName, $exception)
            );

            $newState = $callback->onFailure($exception, $context, $this);
        }
        Assert::string($newState);

        $this->setCurrentState($newState);

        $callback->afterStateChange($context, $this);

        $this->listeners->dispatch(
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
        if (! $this->states->hasState($stateName)) {
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
        return $this->states->getState($this->currentState)->hasAttribute($attribute);
    }

    /**
     * @param string $event
     * @param \Closure $listener
     */
    public function addListener($event, \Closure $listener)
    {
        Assert::string($event);
        $this->listeners->addListener($event, $listener);
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        $this->states->acceptTransitionVisitor($visitor);
    }

    /**
     * @param StateVisitor $visitor
     */
    public function acceptStateVisitor(StateVisitor $visitor)
    {
        $this->states->acceptStateVisitor($visitor);
    }

    /**
     * @param string $state
     */
    private function setCurrentState($state)
    {
        Assert::string($state);
        $this->currentState = $state;
    }
}
