<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Attribute\StateAttribute;
use Star\Component\State\Attribute\StringAttribute;
use Star\Component\State\Builder\TransitionBuilder;
use Star\Component\State\Event\ContextTransitionWasRequested;
use Star\Component\State\Event\ContextTransitionWasSuccessful;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Webmozart\Assert\Assert;

final class StateMachine
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
     * @var State[]
     */
    private $states = [];

    /**
     * @var StateTransition[]
     */
    private $transitions = [];

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
     * @var TransitionBuilder
     */
    private $transitionFactory;

    /**
     * @param StateContext $context
     */
    public function __construct(StateContext $context)
    {
        $this->dispatcher = new EventDispatcher();
        $this->context = $context;
        $this->failureHandler = new AlwaysThrowException();
        $this->transitionFactory = new TransitionBuilder();
    }

    /**
     * @param StateContext $context
     * @param string $transitionName
     */
    public function transitContext(StateContext $context, $transitionName)
    {
        $from = $context->getCurrentState();
        $transition = $this->getTransition($transitionName);

        if (! $transition->hasChanged($from)) {
            return; // no changes detected, do not trigger transition
        }

        if ($transition->isAllowed($context)) {
            // custom event for transition
            $this->dispatcher->dispatch(
                sprintf(
                    StateEventStore::CUSTOM_EVENT_BEFORE,
                    $context->contextAlias(),
                    $transition->name()
                ),
                new ContextTransitionWasRequested($context)
            );

            $this->dispatcher->dispatch(
                StateEventStore::BEFORE_TRANSITION,
                new TransitionWasRequested($transition)
            );

            $transition->applyStateChange($this->context);

            $this->dispatcher->dispatch(
                StateEventStore::AFTER_TRANSITION,
                new TransitionWasSuccessful($transition)
            );

            // custom event for transition
            $this->dispatcher->dispatch(
                sprintf(
                    StateEventStore::CUSTOM_EVENT_AFTER,
                    $context->contextAlias(),
                    $transition->name()
                ),
                new ContextTransitionWasSuccessful($context)
            );

            return;
        }

        $this->failureHandler->handleNotAllowedTransition($context, $transition);
    }

//    /**
//     * @param string $from
//     * @param string $to
//     *
//     * @return bool
//     */
//    public function isAllowed($from, $to)
//    {
//        Assert::string($from, "From state must be a string, got '%s'.");
//        Assert::string($to, "To state must be a string, got '%s'.");
//
//        return isset($this->whitelist[$from][$to]);
//    }

    /**
     * Returns whether the context's state evaluate to the $state.
     *
     * @param State|string $state
     * @param StateContext $context todo remove and use class attribute
     *
     * @return bool
     */
//    public function isState($state, StateContext $context)
//    {
//        $state = static::state($state);
//
//        return $state->matchState($context->getCurrentState());
//    }

    /**
     * @param string $name The name of the attribute to add for $state.
     * @param string|string[] $state State name or names of state(s) to add the attribute to.
     * @param mixed $value The optional value of the attribute.
     *
     * @return StateMachine
     */
    public function addAttribute($name, $state, $value = null)
    {
        $states = (array) $state;
        // todo use factory
        $attribute = new StringAttribute($name, $value);

        foreach ($states as $state) {
            $this->getState($state)->addAttribute($attribute);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $from
     * @param string $to
     *
     * @return StateMachine
     */
    public function addTransition($name, $from, $to)
    {
        $alias = $this->context->contextAlias();
        $transition = $this->transitionFactory->createTransition($name, $from, $to);
        $this->transitions[$alias][$name] = $transition;

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
//        foreach ($attributes as $attr) {
//            $this->attributes[$state->toString()][$attribute->name()] = 1;
//        }

        return $this;
    }

    /**
     * @param StateAttribute|string $attribute
     *
     * @return bool
     */
    public function hasAttribute($attribute)
    {
        $state = $this->context->getCurrentState();

        return $state->hasAttribute($attribute);
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
     * @param string $name
     *
     * @return State
     * @throws NotFoundException
     */
    private function getState($name)
    {
        $alias = $this->context->contextAlias();
        if (! $this->states[$alias][$name]) {
            throw NotFoundException::stateNotFound($name, $alias);
        }

        return $this->states[$alias][$name];
    }

    /**
     * @param string $name
     *
     * @return StateTransition
     * @throws NotFoundException
     */
    private function getTransition($name)
    {
        $alias = $this->context->contextAlias();
        if (! isset($this->transitions[$alias][$name])) {
            throw NotFoundException::transitionNotFound($name, $alias);
        }

        return $this->transitions[$alias][$name];
    }

    /**
     * @param State $from
     * @param State $to
     */
    private function whitelistTransition(State $from, $to) {
        $to = static::state($to);
        $this->whitelist[$from->name()][$to->name()] = 1;
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
