<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Events\StateEventStore;
use Star\Component\State\Events\TransitionWasPerformed;
use Star\Component\State\Events\TransitionWasRequested;
use Symfony\Component\EventDispatcher\EventDispatcher;

class StateMachine
{
    const ALLOWED = 1;

    /**
     * @var array
     *
     * $whitelist = [
     *     'state1' => [
     *         'state2' => callable,
     *         'state3' => callable,
     *     ],
     *     'state2' => [
     *         'state1 => callable,
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
     * @param StateContext $context
     */
    public function __construct(StateContext $context)
    {
        $this->dispatcher = new EventDispatcher();
        $this->context = $context;
    }

    /**
     * @param StateContext $context
     * @param State|string $to
     * @throws InvalidGameTransitionException
     */
    public function transitContext(StateContext $context, $to)
    {
        $to = self::state($to);
        $from = $context->getCurrentState();

        if ($from->matchState($to)) {
            return; // no changes detected, do not trigger transition
        }

        if ($this->isAllowed($from, $to)) {
            // custom event for transition
            $this->dispatcher->dispatch(
                sprintf(
                    'before.%s.%s_to_%s',
                    $context->contextAlias(),
                    $from->toString(),
                    $to->toString()
                ),
                new TransitionWasRequested($from, $to)
            );

            $this->dispatcher->dispatch(
                StateEventStore::BEFORE_TRANSITION,
                new TransitionWasRequested($from, $to)
            );

            $context->setState($to);

            $this->dispatcher->dispatch(
                StateEventStore::AFTER_TRANSITION,
                new TransitionWasPerformed($from, $to)
            );

            // custom event for transition
            $this->dispatcher->dispatch(
                sprintf(
                    'after.%s.%s_to_%s',
                    $context->contextAlias(),
                    $from->toString(),
                    $to->toString()
                ),
                new TransitionWasRequested($from, $to)
            );

            return;
        }

        // todo add handler for not allowed transition (default to exception)
        throw InvalidGameTransitionException::invalidTransition($from, $to);
    }

    /**
     * @param State|string $from
     * @param State|string $to
     *
     * @return bool
     */
    public function isAllowed($from, $to)
    {
        $from = self::state($from);
        $to = self::state($to);

        return isset($this->whitelist[$from->toString()][$to->toString()]);
    }

    /**
     * @param State|string $state
     * @param StateContext $context
     *
     * @return bool
     */
    public function isState($state, StateContext $context)
    {
        $state = self::state($state);

        return $state->matchState($context->getCurrentState());
    }

    /**
     * @param State|string $from
     * @param State|string $to
     * @param callable $beforeCallback A callback to run before the transition
     * @param callable $afterCallback A callback to run after the transition
     *
     * @return StateMachine
     */
    public function whitelist(
        $from,
        $to,
        \Closure $beforeCallback = null,
        \Closure $afterCallback = null
    ) {
        $from = self::state($from);
        if (is_array($to)) {
            foreach ($to as $_to) {
                $this->whitelistTransition($from, $_to, $beforeCallback, $afterCallback);
            }
        } else {
            $this->whitelistTransition($from, $to, $beforeCallback, $afterCallback);
        }

        return $this;
    }

    /**
     * @param State $from
     * @param State $to
     * @param callable $beforeCallback
     * @param callable $afterCallback
     */
    private function whitelistTransition(
        State $from,
        $to,
        \Closure $beforeCallback = null,
        \Closure $afterCallback = null
    ) {
        $to = self::state($to);
        $this->whitelist[$from->toString()][$to->toString()] = self::ALLOWED;

        if ($beforeCallback) {
            $this->dispatcher->addListener(
                StateEventStore::BEFORE_TRANSITION,
                $beforeCallback
            );
        }

        if ($afterCallback) {
            $this->dispatcher->addListener(
                StateEventStore::AFTER_TRANSITION,
                $afterCallback
            );
        }
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
     * @param StateContext $context
     *
     * @return StateMachine
     */
    public static function create(StateContext $context)
    {
        return new self($context);
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
