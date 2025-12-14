<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Context\TestContextWithInterface;
use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Callbacks\BufferStateChanges;
use Star\Component\State\Context\TestStubContext;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasFailed;
use Star\Component\State\Event\TransitionWasRequested;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Stub\EventRegistrySpy;
use Star\Component\State\Transitions\OneToOneTransition;
use stdClass;
use Throwable;

final class StateMachineTest extends TestCase
{
    private TransitionRegistry $registry;
    private StateMachine $machine;
    private TestContext $context;
    private EventRegistrySpy $events;

    public function setUp(): void
    {
        $this->events = new EventRegistrySpy();
        $this->context = new TestContext();
        $this->registry = new TransitionRegistry();
        $this->machine = new StateMachine('current', $this->registry, $this->events);
    }

    public function test_it_should_not_allow_to_transition_to_a_not_configured_transition(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('The transition \'not-configured\' could not be found.');
        $this->machine->transit('not-configured', $this->context);
    }

    public function test_it_should_transition_from_one_state_to_the_other(): void
    {
        $this->registry->addTransition(new OneToOneTransition('name', 'current', 'next'));
        self::assertTrue($this->machine->isInState('current'));

        self::assertSame('next', $this->machine->transit('name', $this->context));

        self::assertFalse($this->machine->isInState('current'));
    }

    public function test_it_should_trigger_an_event_before_any_transition(): void
    {
        $this->registry->addTransition(new OneToOneTransition('name', 'current', 'next'));
        $this->machine->transit('name', $this->context);
        $name = StateEventStore::BEFORE_TRANSITION;
        $events = $this->events->getDispatchedEvents($name);
        self::assertCount(1, $events);
        self::assertContainsOnlyInstancesOf(TransitionWasRequested::class, $events);
    }

    public function test_it_should_trigger_an_event_after_any_transition(): void
    {
        $this->registry->addTransition(new OneToOneTransition('name', 'current', 'next'));
        $this->machine->transit('name', $this->context);
        $name = StateEventStore::AFTER_TRANSITION;
        $events = $this->events->getDispatchedEvents($name);
        self::assertCount(1, $events);
        self::assertContainsOnlyInstancesOf(TransitionWasSuccessful::class, $events);
    }

    public function test_it_should_throw_exception_with_class_context_when_transition_not_allowed(): void
    {
        $this->registry->addTransition(new OneToOneTransition('t', 'start', 'end'));
        self::assertFalse($this->machine->isInState('start'));

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 't' is not allowed when context 'stdClass' is in state 'current'."
        );
        $this->machine->transit('t', new stdClass);
    }

    public function test_it_should_throw_exception_with_context_as_string_when_transition_not_allowed(): void
    {
        $this->registry->addTransition(new OneToOneTransition('transition', 'start', 'end'));
        self::assertFalse($this->machine->isInState('start'));

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 'transition' is not allowed when context 'c' is in state 'current'."
        );
        $this->machine->transit('transition', 'c');
    }

    public function test_state_can_have_attribute(): void
    {
        $this->registry->registerStartingState('transition', 'current', ['exists']);
        self::assertFalse($this->machine->hasAttribute('not-exists'));
        self::assertTrue($this->machine->hasAttribute('exists'));
    }

    public function test_it_should_visit_the_transitions(): void
    {
        $registry = $this->createMock(StateRegistry::class);
        $machine = new StateMachine('', $registry, $this->events);
        $visitor = $this->createStub(TransitionVisitor::class);

        $registry
            ->expects($this->once())
            ->method('acceptTransitionVisitor')
            ->with($visitor);
        $machine->acceptTransitionVisitor($visitor);
    }

    public function test_it_should_throw_exception_when_state_do_not_exists(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("The state 'not-exists' could not be found.");
        $this->machine->isInState('not-exists');
    }

    public function test_it_should_dispatch_an_event_before_a_transition_has_failed(): void
    {
        $this->registry->addTransition(new OneToOneTransition('t', 'from', 'to'));
        try {
            $this->machine->transit('t', 'context');
            $this->fail('An exception should have been thrown');
        } catch (Throwable $exception) {
            // silence it
            self::assertInstanceOf(InvalidStateTransitionException::class, $exception);
        }

        $name = StateEventStore::FAILURE_TRANSITION;
        $events = $this->events->getDispatchedEvents($name);
        self::assertCount(1, $events);
        self::assertContainsOnlyInstancesOf(TransitionWasFailed::class, $events);
    }

    public function test_it_should_invoke_before_state_change_callback(): void
    {
        $this->registry->addTransition(new OneToOneTransition('t', 'current', 'to'));
        $buffer = new BufferStateChanges();

        self::assertSame(
            [],
            $buffer->flushBuffer(),
        );

        $this->machine->transit(
            't',
            'context',
            $buffer,
        );

        self::assertSame(
            [
                'context' => [
                    'beforeStateChange',
                    'afterStateChange',
                ],
            ],
            $buffer->flushBuffer(),
        );
    }

    public function test_it_should_allow_to_transit_using_state_context(): void
    {
        $machine = StateBuilder::build(null, $this->events)
            ->allowTransition('activate', 'left', 'right')
            ->create('left');
        $context = new TestStubContext('post');

        $machine->transit(
            'activate',
            $context,
            $callback = new BufferStateChanges(),
        );

        self::assertSame(
            [

            ],
            $this->events->getDispatchedEvents('ddsa')
        );
        self::assertSame(
            [

            ],
            $callback->flushBuffer(),
        );

        self::fail('todo');
    }

    public function test_it_should_allow_handle_failure_with_state_context(): void
    {
        self::fail('todo');
    }
}
