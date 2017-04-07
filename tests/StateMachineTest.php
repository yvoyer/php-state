<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Event\StateEventStore;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
use Star\Component\State\States\StringState;
use Star\Component\State\Transitions\AllowedTransition;

final class StateMachineTest extends \PHPUnit_Framework_TestCase
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
     * @var StateContext
     */
    private $context;

    public function setUp() {
        $this->context = new TestContext('current');
        $this->registry = new TransitionRegistry();
        $this->registry->addState(new StringState('current'));
        $this->machine = new StateMachine('current', $this->registry);
        $this->registry->addTransition(
            new AllowedTransition(
                'name',
                new StringState('current'),
                new StringState('next')
            )
        );
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
        $this->assertTrue($this->machine->isInState('current', $this->context));

        $this->assertSame('next', $this->machine->transitContext('name', $this->context));

        $this->assertFalse($this->machine->isInState('current', $this->context));
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
     * @expectedExceptionMessage The transition 'transition' is not allowed on context 'context'.
     */
    public function test_it_should_throw_exception_when_transition_not_allowed()
    {
        $this->assertFalse($this->machine->isInState('not-allowed', $this->context));
        $this->machine->transitContext('not-allowed', $this->context);
    }

    public function test_state_can_have_attribute()
    {
        $this->assertFalse($this->machine->hasAttribute('not-exists'));

        $this->registry->addState(new StringState('name', ['not-exists']));

        $this->assertFalse($this->machine->hasAttribute('not-exists'));
    }
}
