<?php

namespace Star\Component\State\Visitor;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Transitions\ManyToOneTransition;
use Star\Component\State\Transitions\OneToOneTransition;

final class TransitionDumperTest extends TestCase
{
    /**
     * @var TransitionDumper
     */
    private $dumper;

    public function setUp()
    {
        $this->dumper = new TransitionDumper();
    }

    public function test_it_should_return_the_structure_when_one_to_one()
    {
        $transition = new OneToOneTransition('t1', 's1', 's2');
        $transition->acceptTransitionVisitor($this->dumper);
        $this->assertEquals(
            [
                't1' => [
                    'from' => [
                        's1',
                    ],
                    'to' => [
                        's2',
                    ],
                ],
            ],
            $this->dumper->getStructure()
        );
    }

    public function test_it_should_return_the_structure_when_many_to_one()
    {
        $transition = new ManyToOneTransition('t1', ['s1', 's2'], 's3');
        $transition->acceptTransitionVisitor($this->dumper);
        $this->assertEquals(
            [
                't1' => [
                    'from' => [
                        's1',
                        's2',
                    ],
                    'to' => [
                        's3',
                    ],
                ],
            ],
            $this->dumper->getStructure()
        );
    }
}