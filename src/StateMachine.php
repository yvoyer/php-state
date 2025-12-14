<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Closure;
use Star\Component\State\Callbacks\AlwaysThrowExceptionOnFailure;
use Star\Component\State\Callbacks\TransitionCallback;
use Star\Component\State\Event\Adapter\ObjectAdapterContext;
use Star\Component\State\Event\Adapter\StringAdapterContext;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasFailed;
use Star\Component\State\Event\TransitionWasRequested;
use Star\Component\State\Event\TransitionWasSuccessful;
use function is_object;
use function is_scalar;

final class StateMachine
{
    private EventRegistry $listeners;
    private StateRegistry $states;
    private string $currentState;

    public function __construct(
        string $currentState,
        StateRegistry $states,
        EventRegistry $listeners
    ) {
        $this->listeners = $listeners;
        $this->states = $states;
        $this->setCurrentState($currentState);
    }

    /**
     * @param string $transitionName The transition name
     * @param string|object|StateContext $context
     * @param TransitionCallback|null $callback
     *
     * @return string The next state to store on your context
     * @throws InvalidStateTransitionException
     * @throws NotFoundException
     */
    public function transit(
        string $transitionName,
        mixed $context,
        ?TransitionCallback $callback = null
    ): string {
        if (!$callback) {
            $callback = new AlwaysThrowExceptionOnFailure();
        }
        if (is_scalar($context)) {
            $context = new StringAdapterContext((string) $context);
        }

        if (is_object($context) && !$context instanceof StateContext) {
            $context = new ObjectAdapterContext($context);
        }

        $previous = $this->currentState;
        $transition = $this->states->getTransition($transitionName);
        $newState = $transition->getDestinationState();

        $this->listeners->dispatch(
            StateEventStore::BEFORE_TRANSITION,
            new TransitionWasRequested(
                $transitionName,
                $previous,
                $newState,
                $context,
            )
        );

        $callback->beforeStateChange($context, $this);

        $allowed = $this->states->transitionStartsFrom($transitionName, $this->currentState);
        if (!$allowed) {
            $exception = InvalidStateTransitionException::notAllowedTransition(
                $transitionName,
                $context,
                $this->currentState
            );

            $this->listeners->dispatch(
                StateEventStore::FAILURE_TRANSITION,
                new TransitionWasFailed(
                    $transitionName,
                    $previous,
                    $newState,
                    $context,
                    $exception,
                )
            );

            $newState = $callback->onFailure($exception, $context, $this);
        }

        $this->setCurrentState($newState);

        $callback->afterStateChange($context, $this);

        $this->listeners->dispatch(
            StateEventStore::AFTER_TRANSITION,
            new TransitionWasSuccessful(
                $transitionName,
                $previous,
                $newState,
                $context,
            )
        );

        return $this->currentState;
    }

    /**
     * @param string $stateName
     * @return bool
     * @throws NotFoundException
     */
    public function isInState(string $stateName): bool
    {
        if (!$this->states->hasState($stateName)) {
            throw NotFoundException::stateNotFound($stateName);
        }

        return $this->currentState === $stateName;
    }

    public function hasAttribute(string $attribute): bool
    {
        return $this->states->hasAttribute($this->currentState, $attribute);
    }

    public function addListener(string $event, Closure $listener): void
    {
        $this->listeners->addListener($event, $listener);
    }

    public function acceptTransitionVisitor(TransitionVisitor $visitor): void
    {
        $this->states->acceptTransitionVisitor($visitor);
    }

    public function acceptStateVisitor(StateVisitor $visitor): void
    {
        $this->states->acceptStateVisitor($visitor);
    }

    private function setCurrentState(string $state): void
    {
        $this->currentState = $state;
    }
}
