<?php

namespace Star\Component\State\Transitions;

use PHPUnit\Framework\TestCase;
use Star\Component\State\StateRegistry;

final class ManyToOneTransitionTest extends TestCase
{
    /**
     * @var ManyToOneTransition
     */
    private $transition;

    public function setUp()
    {
        $this->transition = new ManyToOneTransition('name', ['f1', 'f2'], 'to');
    }

    public function test_it_should_be_allowed_when_from_state_match()
    {
        $this->assertTrue($this->transition->isAllowed('f1'));
        $this->assertTrue($this->transition->isAllowed('f2'));
        $this->assertFalse($this->transition->isAllowed('to'));
    }

    public function test_it_should_register_the_from_and_to_states()
    {
        $registry = $this->getMockBuilder(StateRegistry::class)->getMock();
        $registry
            ->expects($this->at(0))
            ->method('registerState')
            ->with('f1', []);
        $registry
            ->expects($this->at(1))
            ->method('registerState')
            ->with('f2', []);
        $registry
            ->expects($this->at(2))
            ->method('registerState')
            ->with('to', []);

        $this->transition->onRegister($registry);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Expected at least 1 state. Got: 0
     */
    public function test_it_should_throw_exception_when_no_states_are_provided()
    {
        new ManyToOneTransition('name', [], 'to');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Expected a string. Got: array
     */
    public function test_it_should_throw_exception_when_states_are_not_instances()
    {
        new ManyToOneTransition('name', [[]], 'to');
    }
}
