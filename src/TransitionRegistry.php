<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Transitions\ReadOnlyTransition;

final class TransitionRegistry implements StateRegistry
{
    /**
     * @var string[][][]|string[][] Collection of states indexed by transition name
     */
    private array $transitions = [];

    /**
     * @var string[][] Collection of attributes indexed by state name
     */
    private array $states = [];

    /**
     * @param StateTransition $transition
     */
    public function addTransition(StateTransition $transition): void
    {
        $name = $transition->getName();
        if (isset($this->transitions[$name])) {
            throw DuplicateEntryException::duplicateTransition($name);
        }

        $transition->onRegister($this);
    }

    /**
     * @param string $name The transition name
     *
     * @return StateTransition
     * @throws NotFoundException
     */
    public function getTransition(string $name): StateTransition
    {
        $transition = null;
        if (isset($this->transitions[$name]['to'])) {
            /**
             * @var string $to
             */
            $to = $this->transitions[$name]['to'];
            $transition = new ReadOnlyTransition($to);
        }

        if (!$transition) {
            throw NotFoundException::transitionNotFound($name);
        }

        return $transition;
    }

    public function addAttribute(string $state, string $attribute): void
    {
        $attributes = [$attribute];
        if ($this->hasState($state)) {
            $attributes = \array_merge($this->states[$state], $attributes);
        }

        $this->states[$state] = \array_unique($attributes);
    }

    /**
     * @param string $state
     * @param string[] $attributes
     */
    private function addAttributes(string $state, array $attributes): void
    {
        \array_map(
            function($attribute) use ($state) {
                $this->addAttribute($state, $attribute);
            },
            $attributes
        );
    }

    public function transitionStartsFrom(string $transition, string $state): bool
    {
        $from = [];
        if (isset($this->transitions[$transition]['from'])) {
            $from = $this->transitions[$transition]['from'];
        }

        return \in_array($state, $from, true); // @phpstan-ignore-line
    }

    public function hasAttribute(string $state, string $attribute): bool
    {
        if (!$this->hasState($state)) {
            return false;
        }

        return \in_array($attribute, $this->states[$state]);
    }

    public function hasState(string $name): bool
    {
        return \array_key_exists($name, $this->states);
    }

    public function acceptTransitionVisitor(TransitionVisitor $visitor): void
    {
        foreach ($this->transitions as $transition => $states) {
            $visitor->visitTransition($transition);

            foreach ($states['from'] as $from) { // @phpstan-ignore-line
                $visitor->visitFromState($from, $this->states[$from]);
            }
            /**
             * @var string $to
             */
            $to = $states['to'];

            $visitor->visitToState($to, $this->states[$to]);
        }
    }

    public function acceptStateVisitor(StateVisitor $visitor): void
    {
        foreach ($this->states as $state => $attributes) {
            $visitor->visitState($state, $attributes);
        }
    }

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerStartingState(string $transition, string $stateName, array $attributes = []): void
    {
        $this->initState($stateName);
        $this->addAttributes($stateName, $attributes);
        $this->transitions[$transition]['from'][] = $stateName; // @phpstan-ignore-line
    }

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerDestinationState(string $transition, string $stateName, array $attributes = []): void
    {
        $this->initState($stateName);
        $this->addAttributes($stateName, $attributes);
        $this->transitions[$transition]['to'] = $stateName;
    }

    /**
     * @param string $state
     */
    private function initState(string $state): void
    {
        if (!$this->hasState($state)) {
            $this->states[$state] = [];
        }
    }
}
