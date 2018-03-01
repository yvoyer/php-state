<?php

namespace Star\Component\State;

use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Callbacks\TransitionCallback;
use Webmozart\Assert\Assert;

abstract class StateMetadata
{
    /**
     * @var string
     */
    protected $current;

    /**
     * @param string $initial The initial state name
     */
    public function __construct($initial)
    {
        Assert::string($initial);
        $this->current = $initial;
    }

    /**
     * Returns the state workflow configuration.
     *
     * @param StateBuilder $builder
     */
    abstract protected function configure(StateBuilder $builder);

    private function getMachine()
    {
        $this->configure($builder = new StateBuilder());

        // todo implement caching for faster building
        return $builder->create($this->current);
    }

    /**
     * @param string $name
     * @param mixed $context
     * @param TransitionCallback|null $callback
     *
     * @return StateMetadata
     */
    final public function transit($name, $context, TransitionCallback $callback = null)
    {
        $this->current = $this->getMachine()->transit($name, $context, $callback);

        return $this;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    final public function hasAttribute($attribute)
    {
        return $this->getMachine()->hasAttribute($attribute);
    }

    /**
     * @param string $state
     *
     * @return bool
     */
    final public function isInState($state)
    {
        return $this->getMachine()->isInState($state);
    }

    /**
     * @return string
     */
    final public function getCurrent()
    {
        return $this->current;
    }
}
