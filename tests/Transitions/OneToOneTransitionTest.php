<?php

namespace Star\Component\State\Transitions;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Stub\RegistrySpy;

final class OneToOneTransitionTest extends TestCase
{
    /**
     * @var OneToOneTransition
     */
    private $transition;

    public function setUp()
    {
        $this->transition = new OneToOneTransition('name', 'from', 'to');
    }

    public function test_it_should_have_a_name()
    {
        $this->assertSame('name', $this->transition->getName());
    }

    public function test_it_should_register_the_from_and_to_states()
    {
        $registry = new RegistrySpy();

        $this->transition->onRegister($registry);

        $this->assertCount(1, $registry->states['name']['start']);
        $this->assertCount(1, $registry->states['name']['destination']);
    }
}
