<?php declare(strict_types=1);

namespace Star\Component\State\Visitor;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateMachine;

final class AttributeDumperTest extends TestCase
{
    private StateMachine $machine;

    public function setUp(): void
    {
        $this->machine = StateBuilder::build()
            ->allowTransition('t1', 's1', 's2')
            ->allowTransition('t2', ['s2', 's3'], 's1')
            ->addAttribute('a1', 's1')
            ->addAttribute('a2', ['s1', 's2'])
            ->create('s1');
    }

    public function test_it_should_dump_the_attributes(): void
    {
        $this->machine->acceptStateVisitor($dumper = new AttributeDumper());
        self::assertEquals(
            [
                's1' => ['a1', 'a2'],
                's2' => ['a2'],
                's3' => [],
            ],
            $dumper->getStructure()
        );
    }
}
