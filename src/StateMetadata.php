<?php declare(strict_types=1);

namespace Star\Component\State;

use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Callbacks\TransitionCallback;

abstract class StateMetadata
{
    /**
     * @param string $current The initial state name
     */
    public function __construct(
        protected string $current,
    ) {
    }

    /**
     * Returns the state workflow configuration.
     *
     * @param StateBuilder $builder
     */
    abstract protected function configure(StateBuilder $builder): void;

    private function getMachine(): StateMachine
    {
        $this->configure($builder = new StateBuilder());

        // todo implement caching for faster building
        return $builder->create($this->current);
    }

    final public function transit(
        string $name,
        StateContext $context,
        ?TransitionCallback $callback = null
    ): StateMetadata {
        $this->current = $this->getMachine()->transit($name, $context, $callback);

        return $this;
    }

    final public function hasAttribute(string $attribute): bool
    {
        return $this->getMachine()->hasAttribute($attribute);
    }

    final public function isInState(string $state): bool
    {
        return $this->getMachine()->isInState($state);
    }

    final public function getCurrent(): string
    {
        return $this->current;
    }
}
