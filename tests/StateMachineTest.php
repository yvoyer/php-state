<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasFailed;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
use Star\Component\State\Transitions\OneToOneTransition;

final class StateMachineTest extends TestCase
{
    private TransitionRegistry $registry;
    private StateMachine $machine;
    private TestContext $context;
    /**
     * @var MockObject|EventRegistry
     */
    private $listeners;

    public function setUp(): void
    {
        $this->listeners = $this->createMock(EventRegistry::class);
        $this->context = new TestContext();
        $this->registry = new TransitionRegistry();
        $this->machine = new StateMachine('current', $this->registry, $this->listeners);
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
        $this->assertTrue($this->machine->isInState('current'));

        $this->assertSame('next', $this->machine->transit('name', $this->context));

        $this->assertFalse($this->machine->isInState('current'));
    }

    public function test_it_should_trigger_an_event_before_any_transition(): void
    {
        $this->listeners
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                StateEventStore::BEFORE_TRANSITION,
                $this->isInstanceOf(TransitionWasRequested::class)
            );

        $this->registry->addTransition(new OneToOneTransition('name', 'current', 'next'));
        $this->machine->transit('name', $this->context);
    }

    public function test_it_should_trigger_an_event_after_any_transition(): void
    {
        $this->listeners
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                StateEventStore::AFTER_TRANSITION,
                $this->isInstanceOf(TransitionWasSuccessful::class)
            );

        $this->registry->addTransition(new OneToOneTransition('name', 'current', 'next'));
        $this->machine->transit('name', $this->context);
    }

    public function test_it_should_throw_exception_with_class_context_when_transition_not_allowed(): void
    {
        $this->registry->addTransition(new OneToOneTransition('t', 'start', 'end'));
        $this->assertFalse($this->machine->isInState('start'));

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 't' is not allowed when context 'stdClass' is in state 'current'."
        );
        $this->machine->transit('t', new \stdClass);
    }

    public function test_it_should_throw_exception_with_context_as_string_when_transition_not_allowed(): void
    {
        $this->registry->addTransition(new OneToOneTransition('transition', 'start', 'end'));
        $this->assertFalse($this->machine->isInState('start'));

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 'transition' is not allowed when context 'c' is in state 'current'."
        );
        $this->machine->transit('transition', 'c');
    }

    public function test_state_can_have_attribute(): void
    {
        $this->registry->registerStartingState('transition', 'current', ['exists']);
        $this->assertFalse($this->machine->hasAttribute('not-exists'));
        $this->assertTrue($this->machine->hasAttribute('exists'));
    }

    public function test_it_should_visit_the_transitions(): void
    {
        $registry = $this->createMock(StateRegistry::class);
        $machine = new StateMachine('', $registry, $this->listeners);
        $visitor = $this->createMock(TransitionVisitor::class);

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
        $this->listeners
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                StateEventStore::FAILURE_TRANSITION,
                $this->isInstanceOf(TransitionWasFailed::class)
            );

        $this->registry->addTransition(new OneToOneTransition('t', 'from', 'to'));
        try {
            $this->machine->transit('t', 'context');
            $this->fail('An exception should have been thrown');
        } catch (InvalidStateTransitionException $exception) {
            // silence it
        }
    }
}
