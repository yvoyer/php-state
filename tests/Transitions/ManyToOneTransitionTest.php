<?php declare(strict_types=1);

namespace Star\Component\State\Transitions;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Stub\RegistrySpy;

final class ManyToOneTransitionTest extends TestCase
{
    private ManyToOneTransition $transition;

    public function setUp(): void
    {
        $this->transition = new ManyToOneTransition('name', 'to', 'f1', 'f2');
    }

    public function test_it_should_have_a_name(): void
    {
        self::assertSame('name', $this->transition->getName());
    }

    public function test_it_should_register_the_from_and_to_states(): void
    {
        $registry = new RegistrySpy();

        $this->transition->onRegister($registry);

        self::assertCount(2, $registry->getStates('name', 'start'));
        self::assertCount(1, $registry->getStates('name', 'destination'));
    }

    public function test_it_should_throw_exception_when_no_states_are_provided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Expected at least 1 state. Got: 0');
        new ManyToOneTransition('name', 'to');
    }
}
