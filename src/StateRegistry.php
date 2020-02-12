<?php declare(strict_types=1);

namespace Star\Component\State;

interface StateRegistry extends RegistryBuilder
{
    /**
     * @param string $name The transition name
     *
     * @return StateTransition
     * @throws NotFoundException
     */
    public function getTransition(string $name): StateTransition;

    public function addAttribute(string $state, string $attribute): void;

    public function hasAttribute(string $state, string $attribute): bool;

    public function hasState(string $name): bool;

    public function transitionStartsFrom(string $transition, string $state): bool;

    public function acceptTransitionVisitor(TransitionVisitor $visitor): void;

    public function acceptStateVisitor(StateVisitor $visitor): void;
}
