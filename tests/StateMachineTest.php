<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
use Star\Component\State\Handlers\ClosureHandler;
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

    public function setUp() {
        $this->context = new TestContext('current');
        $this->registry = new TransitionRegistry();
        $this->registry->registerState('current', ['exists']);
        $this->machine = new StateMachine('current', $this->registry);
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
        /**
         * @var TransitionWasRequested $event
         */
        $event = null;
        $this->machine->addListener(
            StateEventStore::BEFORE_TRANSITION,
            function (TransitionWasRequested $_event) use (&$event) {
                $event = $_event;
            }
        );
        $this->assertNull($event);

        $this->registry->addTransition('name', new OneToOneTransition('current', 'next'));
        $this->machine->transit('name', $this->context);

        $this->assertInstanceOf(TransitionWasRequested::class, $event);
        $this->assertSame('name', $event->transition());
    }

    public function test_it_should_trigger_an_event_after_any_transition()
    {
        /**
         * @var TransitionWasSuccessful $event
         */
        $event = null;
        $this->machine->addListener(
            StateEventStore::AFTER_TRANSITION,
            function (TransitionWasSuccessful $_event) use (&$event) {
                $event = $_event;
            }
        );
        $this->assertNull($event);

        $this->registry->addTransition('name', new OneToOneTransition('current', 'next'));
        $this->machine->transit('name', $this->context);

        $this->assertInstanceOf(TransitionWasSuccessful::class, $event);
        $this->assertSame('name', $event->transition());
    }

    /**
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'transition' is not allowed when context 'Star\Component\State\TestContext' is in state 'current'.
     */
    public function test_it_should_throw_exception_when_transition_not_allowed()
    {
        $transition = $this->getMockBuilder(StateTransition::class)->getMock();

        $this->registry->registerState('not-allowed');
        $this->registry->addTransition('transition', $transition);
        $this->assertFalse($this->machine->isInState('not-allowed'));

        $this->machine->transit('transition', $this->context);
    }

    public function test_state_can_have_attribute()
    {
        $this->assertFalse($this->machine->hasAttribute('not-exists'));
        $this->assertTrue($this->machine->hasAttribute('exists'));
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The custom handler was triggered.
     */
    public function test_it_should_use_supplied_failure_handler_when_transition_not_allowed()
    {
        $this->registry->addTransition(
            'transition',
            $this->getMockBuilder(StateTransition::class)->getMock()
        );
        $this->machine->transit(
            'transition',
            $this->context,
            new ClosureHandler(function() {
                throw new \RuntimeException("The custom handler was triggered.");
            })
        );
    }

    public function test_it_should_visit_the_transitions()
    {
        $registry = $this->getMockBuilder(StateRegistry::class)->getMock();
        $machine = new StateMachine('', $registry);
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
        $machine = new StateMachine('', $registry);
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
}
