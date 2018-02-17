<?php

namespace Star\Component\State\Transitions;

use PHPUnit\Framework\TestCase;
use Star\Component\State\RegistryBuilder;

final class OneToOneTransitionTest extends TestCase
{
    /**
     * @var OneToOneTransition
     */
    private $transition;

    public function setUp()
    {
        $this->transition = new OneToOneTransition('from', 'to');
    }

    public function test_it_should_be_allowed_when_from_state_match()
    {
        $this->assertTrue($this->transition->isAllowed('from'));
        $this->assertFalse($this->transition->isAllowed('to'));
    }

    public function test_it_should_register_the_from_and_to_states()
    {
        $registry = $this->getMockBuilder(RegistryBuilder::class)->getMock();
        $registry
            ->expects($this->at(0))
            ->method('registerState')
            ->with('from', []);
        $registry
            ->expects($this->at(1))
            ->method('registerState')
            ->with('to', []);

        $this->transition->onRegister($registry);
    }
}
