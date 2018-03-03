<?php

namespace Star\Component\State\Transitions;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Stub\RegistrySpy;

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

    public function test_it_should_have_a_name()
    {
        $this->assertSame('name', $this->transition->getName());
    }

    public function test_it_should_register_the_from_and_to_states()
    {
        $registry = new RegistrySpy();

        $this->transition->onRegister($registry);

        $this->assertCount(2, $registry->states['name']['start']);
        $this->assertCount(1, $registry->states['name']['destination']);
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
