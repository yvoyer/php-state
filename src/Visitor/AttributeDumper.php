<?php declare(strict_types=1);

namespace Star\Component\State\Visitor;

use Star\Component\State\StateVisitor;

final class AttributeDumper implements StateVisitor
{
    /**
     * An array having the state name as key and the attributes of this state.
     * @var string[][]
     */
    private $structure = [];

    /**
     * @return string[][]
     */
    public function getStructure(): array
    {
        return $this->structure;
    }

    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function visitState(string $name, array $attributes): void
    {
        if (! isset($this->structure[$name])) {
            $this->structure[$name] = [];
        }

        $this->structure[$name] = array_unique(array_merge($this->structure[$name], $attributes));
    }
}
