<?php declare(strict_types=1);

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Context\StringAdapterContext;
use Star\Component\State\Context\HardCodedTestContext;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Port\Symfony\EventDispatcherAdapter;

final class StateBuilderTest extends TestCase
{
    public function test_it_should_allow_to_transition_to_next_state_when_multiple_state_have_attribute(): void
    {
        $machine = StateBuilder::build()
            ->allowTransition('t1', 'from', 'to')
            ->addAttribute('attr', 'from')
            ->create('from');

        self::assertTrue($machine->isInState('from'));
        $machine->transit('t1', new HardCodedTestContext());
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

        $machine->transit('t1', new HardCodedTestContext());

        self::assertTrue($machine->isInState('to'));
        self::assertFalse($machine->hasAttribute('attr'));
    }

    public function test_it_should_dispatch_event_on_transit(): void
    {
        $i = 0;
        $addition = function () use (&$i): void {
            $i ++;
        };

        $listeners = new EventDispatcherAdapter();
        $listeners->addListener(
            StateEventStore::BEFORE_TRANSITION,
            $addition
        );
        $listeners->addListener(
            StateEventStore::AFTER_TRANSITION,
            $addition
        );

        $machine = StateBuilder::build(listeners: $listeners)
            ->allowTransition('t', 'from', 'to')
            ->create('from');

        $machine->transit('t', new StringAdapterContext('c'));
        self::assertSame(2, $i);
    }
}
