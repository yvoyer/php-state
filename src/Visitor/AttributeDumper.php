<?php

namespace Star\Component\State\Visitor;

use Star\Component\State\StateVisitor;

final class AttributeDumper implements StateVisitor
{
    /**
     * @param string $name
     * @param array $attributes
     */
    public function visitState($name, array $attributes)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }
}
