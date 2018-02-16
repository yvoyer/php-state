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
use Star\Component\State\States\StringState;
use Star\Component\State\Transitions\ManyToOneTransition;
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
        $this->registry->addTransition(new OneToOneTransition('name', 'current', 'next'));
    }

    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The transition 'not-configured' could not be found.
     */
    public function test_it_should_not_allow_to_transition_to_a_not_configured_transition()
    {
        $this->machine->transitContext('not-configured', $this->context);
    }

    public function test_it_should_transition_from_one_state_to_the_other()
    {
        $this->assertTrue($this->machine->isInState('current'));

        $this->assertSame('next', $this->machine->transitContext('name', $this->context));

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

        $this->machine->transitContext('name', $this->context);

        $this->assertInstanceOf(TransitionWasRequested::class, $event);
        $this->assertInstanceOf(StateTransition::class, $event->transition());
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

        $this->machine->transitContext('name', $this->context);

        $this->assertInstanceOf(TransitionWasSuccessful::class, $event);
        $this->assertInstanceOf(StateTransition::class, $event->transition());
    }

    /**
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'transition' is not allowed when context 'Star\Component\State\TestContext' is in state 'current'.
     */
    public function test_it_should_throw_exception_when_transition_not_allowed()
    {
        $this->registry->addTransition(
            new OneToOneTransition('transition', 'not-allowed', 'not-allowed')
        );
        $this->assertFalse($this->machine->isInState('not-allowed'));

        $this->machine->transitContext('transition', $this->context);
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
            new OneToOneTransition('transition', 'not-allowed', 'not-allowed')
        );
        $this->machine->transitContext(
            'transition',
            $this->context,
            new ClosureHandler(function() {
                throw new \RuntimeException("The custom handler was triggered.");
            })
        );
    }

    public function test_it_should_allow_transition_when_can_start_from_multiple_states()
    {
        $this->registry->addTransition(
            new ManyToOneTransition('t', ['other', 'current'], 'to')
        );
        $this->assertInstanceOf(
            StateMachine::class,
            $this->machine->transit('t', $this->context)
        );

        $this->assertTrue($this->machine->isInState('to'));
    }

    public function test_it_should_set_current_state_using_registered_state()
    {
        $this->registry->addTransition(new OneToOneTransition('move', 'current', 'new'));
        $this->assertTrue($this->machine->isInState('current'));
        $this->assertFalse($this->machine->hasAttribute('attr'));

        $this->machine->setCurrentState(new StringState('new', ['attr']));

        $this->assertTrue($this->machine->isInState('new'));
        $this->assertFalse($this->machine->hasAttribute('attr'));
    }
}
