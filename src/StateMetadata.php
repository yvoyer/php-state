<?php

namespace Star\Component\State;

use Star\Component\State\Builder\StateBuilder;

abstract class StateMetadata
{
    /**
     * @var string
     */
    private $current;

    /**
     * Returns the state workflow configuration.
     *
     * @param StateBuilder $builder
     */
    protected abstract function createMachine(StateBuilder $builder);

    /**
     * Returns the initial state at the creation of the context.
     *
     * @return string
     */
    protected abstract function initialState();

    private function getMachine()
    {
        if (! $this->current) {
            $this->current = $this->initialState();
        }

        $this->createMachine($builder = new StateBuilder());

        // todo implement caching for faster building
        return $builder->create($this->current);
    }

    final public function transit($name, StateContext $context) {
        return $this->getMachine()->transit($name, $context);
    }

    final public function hasAttribute($attribute) {
        return $this->getMachine()->hasAttribute($attribute);
    }

    final public function isInState($state) {
        return $this->getMachine()->isInState($state);
    }
}
