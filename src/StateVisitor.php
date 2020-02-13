<?php declare(strict_types=1);

namespace Star\Component\State;

interface StateVisitor
{
    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function visitState(string $name, array $attributes): void;
}
