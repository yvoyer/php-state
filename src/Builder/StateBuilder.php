<?php declare(strict_types=1);

namespace Star\Component\State\Builder;

use Star\Component\State\EventRegistry;
use Star\Component\State\Port\Symfony\EventDispatcherAdapter;
use Star\Component\State\StateMachine;
use Star\Component\State\StateTransition;
use Star\Component\State\TransitionRegistry;
use Star\Component\State\Transitions\ManyToOneTransition;
use Star\Component\State\Transitions\OneToOneTransition;

/**
 * Tool to build the StateMachine.
 */
final class StateBuilder
{
    private TransitionRegistry $registry;
    private EventRegistry $listeners;

    public function __construct()
    {
        $this->registry = new TransitionRegistry();
        $this->listeners = new EventDispatcherAdapter();
    }

    /**
     * @param string $name
     * @param string|string[] $from
     * @param string $to
     *
     * @return StateBuilder
     */
    public function allowTransition(string $name, $from, string $to): StateBuilder
    {
        if (\is_array($from)) {
            $transition = new ManyToOneTransition($name, $to, ...$from);
        } else {
            $transition = new OneToOneTransition($name, $from, $to);
        }

        $this->allowCustomTransition($transition);

        return $this;
    }

    /**
     * @param StateTransition $transition
     */
    public function allowCustomTransition(StateTransition $transition): void
    {
        $this->registry->addTransition($transition);
    }

    /**
     * @param string $attribute The attribute
     * @param string|string[] $states The list of states that this attribute applies to
     *
     * @return StateBuilder
     */
    public function addAttribute(string $attribute, $states): StateBuilder
    {
        $states = (array) $states;
        foreach ($states as $stateName) {
            $this->registry->addAttribute($stateName, $attribute);
        }

        return $this;
    }

    public function create(string $currentState): StateMachine
    {
        return new StateMachine($currentState, $this->registry, $this->listeners);
    }

    public static function build(): StateBuilder
    {
        return new static();
    }
}
