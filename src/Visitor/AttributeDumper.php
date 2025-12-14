<?php declare(strict_types=1);

namespace Star\Component\State\Visitor;

use Star\Component\State\StateVisitor;

final class AttributeDumper implements StateVisitor
{
    /**
     * An array having the state name as key and the attributes of this state.
     * @var array<string, string[]>
     */
    private array $attributesByStates = [];

    /**
     * @return array<string, string[]>
     */
    public function getStructure(): array
    {
        return $this->attributesByStates;
    }

    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function visitState(string $name, array $attributes): void
    {
        $this->attributesByStates[$name] = $attributes;
    }
}
