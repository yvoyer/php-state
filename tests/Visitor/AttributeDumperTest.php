<?php declare(strict_types=1);

namespace Star\Component\State\Visitor;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;

final class AttributeDumperTest extends TestCase
{
    public function test_it_should_dump_the_attributes(): void
    {
        $machine = StateBuilder::build()
            ->allowTransition('t1', 's1', 's2')
            ->allowTransition('t2', ['s2', 's3'], 's1')
            ->addAttribute('a1', 's1')
            ->addAttribute('a2', ['s1', 's2'])
            ->create('s1');
        $machine->acceptStateVisitor($dumper = new AttributeDumper());

        self::assertEquals(
            [
                's1' => ['a1', 'a2'],
                's2' => ['a2'],
                's3' => [],
            ],
            $dumper->getStructure()
        );
    }

    public function test_it_should_dump_unique_attributes_by_state(): void
    {
        $machine = StateBuilder::build()
            ->allowTransition('t1', 's1', 's2')
            ->addAttribute('a1', ['s1', 's2'])
            ->addAttribute('a2', ['s1', 's2'])
            ->addAttribute('a3', ['s1', 's2'])
            ->addAttribute('a4', ['s1', 's2'])
            ->create('s1');
        $machine->acceptStateVisitor($dumper = new AttributeDumper());

        self::assertEquals(
            [
                's1' => ['a1', 'a2', 'a3', 'a4'],
                's2' => ['a1', 'a2', 'a3', 'a4'],
            ],
            $dumper->getStructure()
        );
    }
}
