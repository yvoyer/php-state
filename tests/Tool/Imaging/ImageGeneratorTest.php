<?php

namespace Star\Component\State\Tool\Imaging;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateMachine;

final class ImageGeneratorTest extends TestCase
{
    /**
     * @var ImageGenerator
     */
    private $generator;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $processor;

    /**
     * @var StateMachine
     */
    private $machine;

    /**
     * Expected state structure order
     *
     *  1  2  5 10 17
     *  3  4  6 11 18
     *  7  8  9 12 19
     * 13 14 15 16 20
     * 21 22 23 24 25
     */
    public function setUp()
    {
        $this->machine = $machine = StateBuilder::build()
            ->allowTransition('t1', 's1', 's2')
            ->allowTransition('t2', 's3', 's4')
            ->allowTransition('t3', 's5', 's6')
            ->allowTransition('t4', 's7', 's8')
            ->allowTransition('t5', 's9', 's1')
            ->create('s1');

        $this->generator = new ImageGenerator(
            $this->processor = $this->getMockBuilder(ImageProcessor::class)->getMock()
        );
    }

    public function test_it_should_put_the_first_state_on_top_left_corner()
    {
        $this->processor
            ->expects($this->at(0))
            ->method('drawState')
            ->with('s1', new Coordinate(100, 100));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_second_state_right_of_first_state()
    {
        $this->processor
            ->expects($this->at(1))
            ->method('drawState')
            ->with('s2', new Coordinate(200, 100));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_third_state_below_first_state()
    {
        $this->processor
            ->expects($this->at(2))
            ->method('drawState')
            ->with('s3', new Coordinate(100, 200));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_fourth_state_under_second_state()
    {
        $this->processor
            ->expects($this->at(3))
            ->method('drawState')
            ->with('s4', new Coordinate(200, 200));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_fifth_state_right_of_second_state()
    {
        $this->processor
            ->expects($this->at(4))
            ->method('drawState')
            ->with('s5', new Coordinate(300, 100));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_sixth_state_under_fifth_state()
    {
        $this->processor
            ->expects($this->at(5))
            ->method('drawState')
            ->with('s6', new Coordinate(300, 200));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_seventh_state_under_third_state()
    {
        $this->processor
            ->expects($this->at(6))
            ->method('drawState')
            ->with('s7', new Coordinate(100, 300));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_eight_state_under_fourth_state()
    {
        $this->processor
            ->expects($this->at(7))
            ->method('drawState')
            ->with('s8', new Coordinate(200, 300));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_ninth_state_under_sixth_state()
    {
        $this->processor
            ->expects($this->at(8))
            ->method('drawState')
            ->with('s9', new Coordinate(300, 300));

        $this->generator->generate('path', $this->machine);
    }

    public function test_it_should_put_the_twenty_state_under_sixteenth()
    {
        $machine = StateBuilder::build()
            ->allowTransition('t1', 's1', 's2')
            ->allowTransition('t2', 's3', 's4')
            ->allowTransition('t3', 's5', 's6')
            ->allowTransition('t4', 's7', 's8')
            ->allowTransition('t5', 's9', 's10')
            ->allowTransition('t6', 's11', 's12')
            ->allowTransition('t7', 's13', 's14')
            ->allowTransition('t8', 's15', 's16')
            ->allowTransition('t9', 's17', 's18')
            ->allowTransition('t10', 's19', 's20')
            ->create('s1');

        $this->processor
            ->expects($this->at(19))
            ->method('drawState')
            ->with('s20', new Coordinate(500, 400));

        $this->generator->generate('path', $machine);
    }
}
