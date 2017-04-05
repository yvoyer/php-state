<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

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

	/**
	 * @var State
	 */
	private $current;

	/**
	 * @param string $current
	 * @param TransitionRegistry|null $registry
	 */
    public function __construct($current, TransitionRegistry $registry = null)
    {
	    if (! $registry) {
		    $registry = new TransitionRegistry();
	    }
        $this->dispatcher = new EventDispatcher();
        $this->failureHandler = new AlwaysThrowException();
        $this->registry = $registry;
	    $this->current = $this->registry->getState($current);
    }

    /**
     * @param string $name
     * @param StateContext $context
     */
    public function transitContext($name, StateContext $context)
    {
//        $this->registry->useFailureHandler($this->failureHandler);
        $transition = $this->registry->getTransition($name);

//        if (! $transition->changeIsRequired($context)) {
//            return; // no changes detected, do not trigger transition
//        }

        if (! $transition->isAllowed($this, $context)) {
            $this->failureHandler->handleTransitionNotAllowed($context, $transition);
        }

        // custom event for transition
//        $this->dispatcher->dispatch(
//            StateEventStore::preTransitionEvent($transition->name(), $context->contextAlias()),
//            new ContextTransitionWasRequested($context)
//        );
//
//        $this->dispatcher->dispatch(
//            StateEventStore::BEFORE_TRANSITION,
//            new TransitionWasRequested($transition)
//        );

        $transition->onStateChange($context);

//        $this->dispatcher->dispatch(
//            StateEventStore::AFTER_TRANSITION,
//            new TransitionWasSuccessful($transition)
//        );
//
        // custom event for transition
//        $this->dispatcher->dispatch(
//            StateEventStore::postTransitionEvent($transition->name(), $context->contextAlias()),
//            new ContextTransitionWasSuccessful($context)
//        );
    }

	/**
	 * @param string $transitionName
	 * @param StateContext $context
	 *
	 * @return bool
	 */
	private function isAllowed($transitionName, StateContext $context)
	{
		$transition = $this->registry->getTransition($transitionName);

		return $transition->isAllowed($context);
	}

	/**
	 * @param string $stateName
	 * @param StateContext $context
	 *
	 * @return bool
	 */
	public function is($stateName, StateContext $context)
	{
		return $this->current->matchState($this->registry->getState($stateName));
	}
}
