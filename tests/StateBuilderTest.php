<?php declare(strict_types=1);

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;

final class StateBuilderTest extends TestCase
{
    public function test_it_should_allow_to_transition_to_next_state_when_multiple_state_have_attribute(): void
    {
        $machine = StateBuilder::build()
            ->allowTransition('t1', 'from', 'to')
            ->addAttribute('attr', 'from')
            ->create('from');

        self::assertTrue($machine->isInState('from'));
        $machine->transit('t1', new TestContext());
        self::assertTrue($machine->isInState('to'));
    }

    public function test_it_should_return_whether_the_current_state_has_attribute_after_transition(): void
    {
        $machine = StateBuilder::build()
            ->allowTransition('t1', 'from', 'to')
            ->addAttribute('attr', 'from')
            ->create('from');

        self::assertTrue($machine->isInState('from'));
        self::assertTrue($machine->hasAttribute('attr'));

        $machine->transit('t1', new TestContext());

        self::assertTrue($machine->isInState('to'));
        self::assertFalse($machine->hasAttribute('attr'));
    }
}
