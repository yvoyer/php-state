<?php declare(strict_types=1);

namespace Star\Component\State;

interface TransitionVisitor
{
    public function visitTransition(string $name): void;

    /**
     * @param string $state
     * @param string[] $attributes
     */
    public function visitFromState(string $state, array $attributes): void;

    /**
     * @param string $state
     * @param string[] $attributes
     */
    public function visitToState(string $state, array $attributes): void;
}
