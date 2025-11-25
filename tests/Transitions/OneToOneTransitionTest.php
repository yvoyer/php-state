<?php declare(strict_types=1);

namespace Star\Component\State\Transitions;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Stub\RegistryBuilderSpy;

final class OneToOneTransitionTest extends TestCase
{
    private OneToOneTransition $transition;

    public function setUp(): void
    {
        $this->transition = new OneToOneTransition('name', 'from', 'to');
    }

    public function test_it_should_have_a_name(): void
    {
        self::assertSame('name', $this->transition->getName());
    }

    public function test_it_should_register_the_from_and_to_states(): void
    {
        $registry = new RegistryBuilderSpy();

        $this->transition->onRegister($registry);

        self::assertCount(1, $registry->getStates('name', 'start'));
        self::assertCount(1, $registry->getStates('name', 'destination'));
    }
}
