<?php declare(strict_types=1);

namespace Star\Component\State\Visitor;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateMachine;

final class TransitionDumperTest extends TestCase
{
    /**
     * @var TransitionDumper
     */
    private $dumper;

    /**
     * @var StateMachine
     */
    private $machine;

    public function setUp(): void
    {
        $this->machine = StateBuilder::build()
            ->allowTransition('t1', 's1', 's2')
            ->allowTransition('t2', ['s2', 's3'], 's1')
            ->addAttribute('a1', 's1')
            ->addAttribute('a2', ['s1', 's2'])
            ->create('s1');
        $this->dumper = new TransitionDumper();
    }

    public function test_it_should_return_the_structure_when_one_to_one(): void
    {
        $this->machine->acceptTransitionVisitor($this->dumper);
        $this->assertArrayHasKey('t1', $this->dumper->getStructure());
        $this->assertEquals(
            [
                'from' => ['s1'],
                'to' => ['s2'],
            ],
            $this->dumper->getStructure()['t1']
        );
    }

    public function test_it_should_return_the_structure_when_many_to_one(): void
    {
        $this->machine->acceptTransitionVisitor($this->dumper);
        $this->assertArrayHasKey('t2', $this->dumper->getStructure());
        $this->assertEquals(
            [
                'from' => [
                    's2',
                    's3',
                ],
                'to' => [
                    's1',
                ],
            ],
            $this->dumper->getStructure()['t2']
        );
    }
}
