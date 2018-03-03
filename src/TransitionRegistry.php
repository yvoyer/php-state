<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Transitions\ReadOnlyTransition;
use Webmozart\Assert\Assert;

final class TransitionRegistry implements StateRegistry
{
    /**
     * @var array[] Collection of states indexed by transition name
     */
    private $transitions = [];

    /**
     * @var array[] Collection of attributes indexed by state name
     */
    private $states = [];

    /**
     * @param StateTransition $transition
     */
    public function addTransition(StateTransition $transition)
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
    public function getTransition($name)
    {
        Assert::string($name);
        $transition = null;
        if (isset($this->transitions[$name]['to'])) {
            $transition = new ReadOnlyTransition($this->transitions[$name]['to']);
        }

        if (! $transition) {
            throw NotFoundException::transitionNotFound($name);
        }

        return $transition;
    }

    /**
     * @param string $state
     * @param string $attribute
     */
    public function addAttribute($state, $attribute)
    {
        $attributes = [$attribute];
        if ($this->hasState($state)) {
            $attributes = array_merge($this->states[$state], $attributes);
        }

        $this->states[$state] = array_unique($attributes);
    }

    /**
     * @param string $state
     * @param string[] $attributes
     */
    private function addAttributes($state, array $attributes)
    {
        array_map(
            function ($attribute) use ($state) {
                $this->addAttribute($state, $attribute);
            },
            $attributes
        );
    }

    /**
     * @param string $transition
     * @param string $state
     *
     * @return bool
     */
    public function transitionStartsFrom($transition, $state)
    {
        Assert::string($transition);
        Assert::string($state);
        $from = [];
        if (isset($this->transitions[$transition]['from'])) {
            $from = $this->transitions[$transition]['from'];
        }

        return in_array($state, $from, true);
    }

    /**
     * @param string $state
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttribute($state, $attribute)
    {
        Assert::string($state);
        Assert::string($attribute);
        if (! $this->hasState($state)) {
            return false;
        }

        return in_array($attribute, $this->states[$state]);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasState($name)
    {
        return array_key_exists($name, $this->states);
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        foreach ($this->transitions as $transition => $states) {
            $visitor->visitTransition($transition);

            foreach ($states['from'] as $from) {
                $visitor->visitFromState($from, $this->states[$from]);
            }
            $visitor->visitToState($states['to'], $this->states[$states['to']]);
        }
    }

    /**
     * @param StateVisitor $visitor
     */
    public function acceptStateVisitor(StateVisitor $visitor)
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
    public function registerStartingState($transition, $stateName, array $attributes = [])
    {
        $this->initState($stateName);
        $this->addAttributes($stateName, $attributes);
        $this->transitions[$transition]['from'][] = $stateName;
    }

    /**
     * @param string $transition
     * @param string $stateName
     * @param string[] $attributes
     */
    public function registerDestinationState($transition, $stateName, array $attributes = [])
    {
        $this->initState($stateName);
        $this->addAttributes($stateName, $attributes);
        $this->transitions[$transition]['to'] = $stateName;
    }

    /**
     * @param string $state
     */
    private function initState($state)
    {
        if (!$this->hasState($state)) {
            $this->states[$state] = [];
        }
    }
}
