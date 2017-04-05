<?php

namespace Star\Component\State\Builder;

use Star\Component\State\StateMachine;
use Star\Component\State\AllowedTransition;
use Star\Component\State\StringState;
use Star\Component\State\TransitionRegistry;

/**
 * Tool to build the StateMachine.
 */
final class StateBuilder {
	/**
	 * @var TransitionRegistry
	 */
	private $registry;

	private function __construct() {
		$this->registry = new TransitionRegistry();
	}

	/**
	 * @param string $name
	 * @param string $from
	 * @param string $to
	 *
	 * @return StateBuilder
	 */
	public function allowTransition($name, $from, $to) {
		$this->registry->addTransition(new AllowedTransition($name, new StringState($from), new StringState($to)));

		return $this;
	}

	/**
	 * @param string $currentState
	 *
	 * @return StateMachine
	 */
	public function create($currentState) {
		return new StateMachine($currentState, $this->registry);
	}

	/**
	 * @return StateBuilder
	 */
	public static function build()
	{
		return new static();
	}
}
