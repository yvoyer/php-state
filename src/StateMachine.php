<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Attribute\StringAttribute;
use Star\Component\State\Event\ContextTransitionWasRequested;
use Star\Component\State\Event\ContextTransitionWasSuccessful;
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
     * @var FailureHandler
     */
    private $failureHandler;

    /**
     * @var TransitionRegistry
     */
    private $registry;

    public function __construct()
    {
        $this->dispatcher = new EventDispatcher();
        $this->failureHandler = new AlwaysThrowException();
//        $this->transitionFactory = new TransitionBuilder();
        $this->registry = new TransitionRegistry();
    }

    /**
     * @param string $name
     * @param StateContext $context
     */
    public function transitContext($name, StateContext $context)
    {
        $alias = $context->contextAlias();
        $this->registry->useFailureHandler($this->failureHandler);
        $transition = $this->registry->getTransition($name, $alias);

        if (! $transition->changeIsRequired($context)) {
            return; // no changes detected, do not trigger transition
        }

        if (! $transition->isAllowed($context)) {
            $this->failureHandler->handleTransitionNotAllowed($context, $transition);
        }

        // custom event for transition
        $this->dispatcher->dispatch(
            StateEventStore::preTransitionEvent($transition->name(), $context->contextAlias()),
            new ContextTransitionWasRequested($context)
        );

        $this->dispatcher->dispatch(
            StateEventStore::BEFORE_TRANSITION,
            new TransitionWasRequested($transition)
        );

        $transition->applyStateChange($context);

        $this->dispatcher->dispatch(
            StateEventStore::AFTER_TRANSITION,
            new TransitionWasSuccessful($transition)
        );

        // custom event for transition
        $this->dispatcher->dispatch(
            StateEventStore::postTransitionEvent($transition->name(), $context->contextAlias()),
            new ContextTransitionWasSuccessful($context)
        );
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
     * @param StateContext $context
     *
     * @return bool
     */
    public function isState($state, StateContext $context)
    {
        $alias = $context->contextAlias();

        return $this
            ->getState($context->getCurrentState()->name(), $alias)
            ->matchState($this->getState($state, $alias));
    }

    /**
     * @param string $context The context alias
     * @param string|string[] $state State name or names of state(s) to add the attribute to.
     * @param string $name The name of the attribute to add for $state.
     * @param mixed $value The optional value of the attribute.
     *
     * @return StateMachine
     */
    public function addAttribute($context, $state, $name, $value = null)
    {
        $states = (array) $state;
        foreach ($states as $state) {
            $this->registry->setAttribute($context, $state, new StringAttribute($name, $value));
        }

        return $this;
    }

    /**
     * @param string $context
     * @param string $name
     * @param string $from
     * @param string $to
     *
     * @return StateMachine
     */
    public function oneToOne($context, $name, $from, $to)
    {
        $this->registry->useFailureHandler($this->failureHandler);
        $this->registry->addTransition(
            $context,
            new OneToOneTransition($name, new StringState($from), new StringState($to))
        );

        return $this;
    }

    /**
     * @param string $context
     * @param string $name
     * @param string $from
     * @param string[] $tos
     *
     * @return StateMachine
     */
    public function oneToMany($context, $name, $from, array $tos)
    {
        $this->registry->addTransition(
            $context,
            new OneToManyTransition(
                $name,
                new StringState($from),
                array_map(
                    function($to) {
                        return new StringState($to);
                    },
                    $tos
                )
            )
        );

        return $this;
    }

    /**
     * @param string $name
     * @param string $context
     *
     * @return State
     * @throws NotFoundException
     */
    public function getState($name, $context)
    {
        return $this->registry->getState($name, $context);
    }

    /**
     * @param string $attribute
     * @param StateContext $context
     *
     * @return bool
     */
    public function hasAttribute($attribute, StateContext $context)
    {
        $state = $this->registry->getState(
            $context->getCurrentState()->name(),
            $context->contextAlias()
        );

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
     * @return StateMachine
     */
    public static function create()
    {
        return new static();
    }
}
