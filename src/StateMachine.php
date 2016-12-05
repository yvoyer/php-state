<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Events\ContextTransitionWasRequested;
use Star\Component\State\Events\ContextTransitionWasSuccessful;
use Star\Component\State\Events\StateEventStore;
use Star\Component\State\Events\TransitionWasSuccessful;
use Star\Component\State\Events\TransitionWasRequested;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StateMachine
{
    /**
     * @var array
     *
     * $whitelist = [
     *     'state1' => [
     *         'state2' => allowed,
     *         'state3' => allowed,
     *     ],
     *     'state2' => [
     *         'state1 => allowed,
     *     ]
     * ];
     */
    private $whitelist = [];

    /**
     * @var EventDispatcher
     */
    private $dispatcher;

    /**
     * @var StateContext
     */
    private $context;

    /**
     * @var FailureHandler
     */
    private $failureHandler;

    /**
     * @param StateContext $context
     */
    public function __construct(StateContext $context)
    {
        $this->dispatcher = new EventDispatcher();
        $this->context = $context;
        $this->failureHandler = new AlwaysThrowException();
    }

    /**
     * @param StateContext $context
     * @param State|string $to
     * @throws InvalidStateTransitionException
     */
    public function transitContext(StateContext $context, $to)
    {
        $to = static::state($to);
        $from = $context->getCurrentState();

        if ($from->matchState($to)) {
            return; // no changes detected, do not trigger transition
        }

        if ($this->isAllowed($from, $to)) {
            // custom event for transition
            $this->dispatcher->dispatch(
                sprintf(
                    StateEventStore::CUSTOM_EVENT_BEFORE,
                    $context->contextAlias(),
                    $from->toString(),
                    $to->toString()
                ),
                new ContextTransitionWasRequested($context)
            );

            $this->dispatcher->dispatch(
                StateEventStore::BEFORE_TRANSITION,
                new TransitionWasRequested($from, $to)
            );

            $context->setState($to);

            $this->dispatcher->dispatch(
                StateEventStore::AFTER_TRANSITION,
                new TransitionWasSuccessful($from, $to)
            );

            // custom event for transition
            $this->dispatcher->dispatch(
                sprintf(
                    StateEventStore::CUSTOM_EVENT_AFTER,
                    $context->contextAlias(),
                    $from->toString(),
                    $to->toString()
                ),
                new ContextTransitionWasSuccessful($context)
            );

            return;
        }

        $this->failureHandler->handleNotAllowedTransition($context, $from, $to);
    }

    /**
     * @param State|string $from
     * @param State|string $to
     *
     * @return bool
     */
    public function isAllowed($from, $to)
    {
        $from = static::state($from);
        $to = static::state($to);

        return isset($this->whitelist[$from->toString()][$to->toString()]);
    }

    /**
     * Returns whether the context's state evaluate to the $state.
     *
     * @param State|string $state
     * @param StateContext $context
     *
     * @return bool
     */
    public function isState($state, StateContext $context)
    {
        $state = static::state($state);

        return $state->matchState($context->getCurrentState());
    }

    /**
     * Add a rule to the white list.
     *
     * @param State|string $from
     * @param State|string $to
     *
     * @return StateMachine
     */
    public function whitelist($from, $to) {
        $from = static::state($from);
        if (is_array($to)) {
            foreach ($to as $_to) {
                $this->whitelistTransition($from, $_to);
            }
        } else {
            $this->whitelistTransition($from, $to);
        }

        return $this;
    }

    /**
     * @param EventSubscriber $subscriber
     *
     * @return StateMachine
     */
    public function addSubscriber(EventSubscriber $subscriber)
    {
        $this->dispatcher->addSubscriber($subscriber);

        return $this;
    }

    /**
     * @param FailureHandler $handler
     *
     * @return StateMachine
     */
    public function useFailureHandler(FailureHandler $handler)
    {
        $this->failureHandler = $handler;

        return $this;
    }

    /**
     * @param State $from
     * @param State $to
     */
    private function whitelistTransition(State $from, $to) {
        $to = static::state($to);
        $this->whitelist[$from->toString()][$to->toString()] = 1;
    }

    /**
     * @param StateContext $context
     *
     * @return StateMachine
     */
    public static function create(StateContext $context)
    {
        return new static($context);
    }

    /**
     * @param State|string $state
     *
     * @return State
     * @throws \InvalidArgumentException
     */
    public static function state($state)
    {
        if ($state instanceof State) {
            return $state;
        }

        if (is_string($state)) {
            return new StringState($state);
        }

        throw new \InvalidArgumentException(
            sprintf(
                "The state of type '%s' is not yet supported.",
                gettype($state)
            )
        );
    }
}
