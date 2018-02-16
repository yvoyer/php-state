<?php

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;

final class StateBuilderTest extends TestCase
{
    /**
     * @var StateContext|\PHPUnit_Framework_MockObject_MockObject
     */
    private $context;

    public function setUp()
    {
        $this->context = $this->getMockBuilder(StateContext::class)->getMock();
    }

    public function test_it_should_allow_to_transition_to_next_state_when_multiple_state_have_attribute()
    {
        $machine = StateBuilder::build()
            ->allowTransition('t1', 'from', 'to')
            ->addAttribute('attr', 'from')
            ->create('from');

        $this->assertTrue($machine->isInState('from'));
        $machine->transit('t1', $this->context);
        $this->assertTrue($machine->isInState('to'));
    }

    public function test_it_should_return_whether_the_current_state_has_attribute_after_transition()
    {
        $machine = StateBuilder::build()
            ->allowTransition('t1', 'from', 'to')
            ->addAttribute('attr', 'from')
            ->create('from');

        $this->assertTrue($machine->isInState('from'));
        $this->assertTrue($machine->hasAttribute('attr'));

        $machine->transit('t1', $this->context);

        $this->assertTrue($machine->isInState('to'));
        $this->assertFalse($machine->hasAttribute('attr'));
    }
}
