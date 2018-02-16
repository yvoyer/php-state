<?php

namespace Star\Component\State;

use Star\Component\State\States\StringState;
use Webmozart\Assert\Assert;

final class ArrayRegistry implements StateRegistry
{
    /**
     * @var State[]
     */
    private $states = [];

    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function registerState($name, array $attributes)
    {
        $state = new StringState($name, $attributes);
        if (isset($this->states[$name])) {
            $state = $this->getState($name);
            $state->addAttributes($attributes);
        }

        $this->states[$name] = $state;
    }

    /**
     * @param string $name The transition name
     *
     * @return StateTransition
     * @throws NotFoundException
     */
    public function getTransition($name)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    /**
     * @param string $name
     * @return State
     * @throws NotFoundException
     */
    public function getState($name)
    {
        Assert::string($name);
        if (! isset($this->states[$name])) {
            throw NotFoundException::stateNotFound($name);
        }

        return $this->states[$name];
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    /**
     * @param StateVisitor $visitor
     */
    public function acceptStateVisitor(StateVisitor $visitor)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }
}
