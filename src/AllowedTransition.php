<?php

namespace Star\Component\State;

use Webmozart\Assert\Assert;

final class AllowedTransition implements StateTransition {
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var State
	 */
	private $from;

	/**
	 * @var State
	 */
	private $to;

	/**
	 * @param string $name
	 * @param State $from
	 * @param State $to
	 */
	public function __construct($name, State $from, State $to) {
		Assert::string($name, 'Transition name should be string, "%s" given.');
		$this->name = $name;
		$this->from = $from;
		$this->to = $to;
	}

	/**
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * @param StateContext $context
	 *
	 * @return bool
	 */
	public function changeIsRequired(StateContext $context) {
		throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
	}

	/**
	 * @param StateMachine $machine
	 * @param StateContext $context
	 *
	 * @return bool
	 */
	public function isAllowed(StateMachine $machine, StateContext $context) {
		return $machine->is($this->from, $context);
	}

	/**
	 * @param TransitionRegistry $registry
	 */
	public function onRegister(TransitionRegistry $registry) {
		$registry->addState($this->from);
		$registry->addState($this->to);
	}

	/**
	 * @param StateContext $context
	 */
	public function beforeStateChange(StateContext $context) {
		throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
	}

	/**
	 * @param StateContext $context
	 */
	public function onStateChange(StateContext $context) {
		$context->setState($this->to);
	}

	/**
	 * @param StateContext $context
	 */
	public function afterStateChange(StateContext $context) {
		throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
	}
}
