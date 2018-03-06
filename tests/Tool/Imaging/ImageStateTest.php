<?php

namespace Star\Component\State\Tool\Imaging;

use PHPUnit\Framework\TestCase;

final class ImageStateTest extends TestCase
{
    /**
     * @var ImagingState
     */
    private $state;

    public function setUp()
    {
        $this->state = new ImagingState('name');
    }

    public function test_it_should_have_a_name()
    {
        $this->assertSame('name', $this->state->getName());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The transition 't' is already defined for the starting state 'name'.
     */
    public function test_it_should_throw_exception_when_duplicated_transition()
    {
        $this->state->addTransition('t', $this->state);
        $this->state->addTransition('t', $this->state);
    }
}
