<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasFailed;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
use Star\Component\State\Transitions\OneToOneTransition;

final class StateMachineTest extends TestCase
{
    /**
     * @var TransitionRegistry
     */
    private $registry;

    /**
     * @var StateMachine
     */
    private $machine;

    /**
     * @var TestContext
     */
    private $context;

    /**
     * @var StateTransition|\PHPUnit_Framework_MockObject_MockObject
     */
    private $transition;

    /**
     * @var EventRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    private $listeners;

    public function setUp()
    {
        $this->listeners = $this->getMockBuilder(EventRegistry::class)->getMock();
        $this->transition = $this->getMockBuilder(StateTransition::class)->getMock();
        $this->context = new TestContext('current');
        $this->registry = new TransitionRegistry();
        $this->registry->registerState('current', ['exists']);
        $this->machine = new StateMachine('current', $this->registry, $this->listeners);
    }

    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The transition 'not-configured' could not be found.
     */
    public function test_it_should_not_allow_to_transition_to_a_not_configured_transition()
    {
        $this->machine->transit('not-configured', $this->context);
    }

    public function test_it_should_transition_from_one_state_to_the_other()
    {
        $this->assertTrue($this->machine->isInState('current'));

        $this->registry->addTransition('name', new OneToOneTransition('current', 'next'));
        $this->assertSame('next', $this->machine->transit('name', $this->context));

        $this->assertFalse($this->machine->isInState('current'));
    }

    public function test_it_should_trigger_an_event_before_any_transition()
    {
        $this->listeners
            ->expects($this->at(0))
            ->method('dispatch')
            ->with(
                StateEventStore::BEFORE_TRANSITION,
                $this->isInstanceOf(TransitionWasRequested::class)
            );

        $this->registry->addTransition('name', new OneToOneTransition('current', 'next'));
        $this->machine->transit('name', $this->context);
    }

    public function test_it_should_trigger_an_event_after_any_transition()
    {
        $this->listeners
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                StateEventStore::AFTER_TRANSITION,
                $this->isInstanceOf(TransitionWasSuccessful::class)
            );

        $this->registry->addTransition('name', new OneToOneTransition('current', 'next'));
        $this->machine->transit('name', $this->context);
    }

    /**
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 't' is not allowed when context 'stdClass' is in state 'current'.
     */
    public function test_it_should_throw_exception_with_class_context_when_transition_not_allowed()
    {
        $this->registry->registerState('not-allowed');
        $this->registry->addTransition('t', $this->transition);
        $this->assertFalse($this->machine->isInState('not-allowed'));

        $this->machine->transit('t', new \stdClass);
    }

    /**
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'transition' is not allowed when context 'c' is in state 'current'.
     */
    public function test_it_should_throw_exception_with_context_as_string_when_transition_not_allowed()
    {
        $this->registry->registerState('not-allowed');
        $this->registry->addTransition('transition', $this->transition);
        $this->assertFalse($this->machine->isInState('not-allowed'));

        $this->machine->transit('transition', 'c');
    }

    public function test_state_can_have_attribute()
    {
        $this->assertFalse($this->machine->hasAttribute('not-exists'));
        $this->assertTrue($this->machine->hasAttribute('exists'));
    }

    public function test_it_should_visit_the_transitions()
    {
        $registry = $this->getMockBuilder(StateRegistry::class)->getMock();
        $machine = new StateMachine('', $registry, $this->listeners);
        $visitor = $this->getMockBuilder(TransitionVisitor::class)->getMock();

        $registry
            ->expects($this->once())
            ->method('acceptTransitionVisitor')
            ->with($visitor);
        $machine->acceptTransitionVisitor($visitor);
    }

    public function test_it_should_visit_the_states()
    {
        $registry = $this->getMockBuilder(StateRegistry::class)->getMock();
        $machine = new StateMachine('', $registry, $this->listeners);
        $visitor = $this->getMockBuilder(StateVisitor::class)->getMock();

        $registry
            ->expects($this->once())
            ->method('acceptStateVisitor')
            ->with($visitor);
        $machine->acceptStateVisitor($visitor);
    }

    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The state 'not-exists' could not be found.
     */
    public function test_it_should_throw_exception_when_state_do_not_exists()
    {
        $this->machine->isInState('not-exists');
    }

    public function test_it_should_dispatch_an_event_before_a_transition_has_failed()
    {
        $this->listeners
            ->expects($this->at(1))
            ->method('dispatch')
            ->with(
                StateEventStore::FAILURE_TRANSITION,
                $this->isInstanceOf(TransitionWasFailed::class)
            );

        $this->registry->addTransition('t', $this->transition);
        try {
            $this->machine->transit('t', 'context');
            $this->fail('An exception should have been thrown');
        } catch (InvalidStateTransitionException $exception) {
            // silence it
        }
    }
}
