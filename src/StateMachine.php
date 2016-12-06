<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Attribute\StateAttribute;
use Star\Component\State\Attribute\StringAttribute;
use Star\Component\State\Event\ContextTransitionWasRequested;
use Star\Component\State\Event\ContextTransitionWasSuccessful;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
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
     * @var StateAttribute[]
     */
    private $attributes = [];

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
     * @param StateContext $context todo remove and use class attribute
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
     * @param StateContext $context todo remove and use class attribute
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
     * @param StateAttribute|string $attribute
     * @param State|string $state
     *
     * @return StateMachine
     */
    public function addAttribute($attribute, $state)
    {
        if (is_array($state)) {
            foreach ($state as $_state) {
                $this->addAttribute($attribute, $_state);
            }
        } else {
            $state = static::state($state);
            $attribute = static::attribute($attribute);
            $this->attributes[$state->toString()][$attribute->name()] = 1;
        }
        // todo add value for attribute?

        return $this;
    }

    /**
     * @param State|string $state
     * @param StateAttribute[]|string[] $attributes
     *
     * @return StateMachine
     */
    public function addAttributes($state, array $attributes)
    {
        foreach ($attributes as $attr) {
            $this->addAttribute($state, $attr);
        }

        return $this;
    }

    /**
     * @param StateAttribute|string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        $attribute = static::attribute($attribute);
        $current = $this->context->getCurrentState();

        return isset($this->attributes[$current->toString()][$attribute->name()]);
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
     * @return State todo return StateBuilder
     * @throws \InvalidArgumentException
     */
    public static function state($state)
    {
        if (is_string($state)) {
            $state = new StringState($state);
        }

        if ($state instanceof State) {
            return $state;
        }

        throw new \InvalidArgumentException(
            sprintf(
                "The state of type '%s' is not yet supported.",
                gettype($state)
            )
        );
    }

    /**
     * @param StateAttribute|string $attribute
     * @param mixed $value
     *
     * @return StringAttribute
     * @throws \InvalidArgumentException
     */
    public static function attribute($attribute, $value = null)
    {
        if (is_string($attribute)) {
            $attribute = new StringAttribute($attribute, $value);
        }

        if ($attribute instanceof StateAttribute) {
            return $attribute;
        }

        throw new \InvalidArgumentException(
            sprintf(
                "Attribute of type '%s' is not yet supported.",
                gettype($attribute)
            )
        );
    }
}
