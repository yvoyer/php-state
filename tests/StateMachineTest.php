<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Event\ContextTransitionWasRequested;
use Star\Component\State\Event\ContextTransitionWasSuccessful;
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
	    $this->registry->addTransition(
		    new AllowedTransition(
			    'name',
			    new StringState('current'),
			    new StringState('next')
		    )
	    );
        $this->assertTrue($this->machine->isInState('current', $this->context));

	    $this->machine->transitContext('name', $this->context);

	    $this->assertFalse($this->machine->isInState('current', $this->context));
	    $this->assertTrue($this->machine->isInState('next', $this->context));
    }

    public function test_it_should_trigger_an_event_before_any_transition()
    {
	    $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'from', 'to')
            ->addSubscriber($subscriber = new TestSubscriber());
        $this->assertNull($subscriber->beforeEvent);

        $machine->transitContext('transition', TestContext::fromString());

        $event = $subscriber->beforeEvent;
        $this->assertInstanceOf(TransitionWasRequested::class, $event);
        $this->assertInstanceOf(StateTransition::class, $event->transition());
    }

    public function test_it_should_trigger_an_event_after_any_transition()
    {
	    $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'from', 'to')
            ->addSubscriber($subscriber = new TestSubscriber());
        $this->assertNull($subscriber->afterEvent);

        $machine->transitContext('transition', TestContext::fromString());

        $event = $subscriber->afterEvent;
        $this->assertInstanceOf(TransitionWasSuccessful::class, $event);
        $this->assertInstanceOf(StateTransition::class, $event->transition());
    }

    public function test_it_should_trigger_a_custom_event_before_a_specific_transition()
    {
	    $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'from', 'to')
            ->addSubscriber($subscriber = new TestSubscriber());
        $this->assertNull($subscriber->beforeContextEvent);

        $machine->transitContext('transition', TestContext::fromString());

        $event = $subscriber->beforeContextEvent;
        $this->assertInstanceOf(ContextTransitionWasRequested::class, $event);
        $this->assertEquals(TestContext::fromString('to'), $event->context());
    }

    public function test_it_should_trigger_a_custom_event_after_a_specific_transition()
    {
	    $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'from', 'to')
            ->addSubscriber($subscriber = new TestSubscriber());
        $this->assertNull($subscriber->afterContextEvent);

        $machine->transitContext('transition', TestContext::fromString());

        $event = $subscriber->afterContextEvent;
        $this->assertInstanceOf(ContextTransitionWasSuccessful::class, $event);
        $this->assertEquals(TestContext::fromString('to'), $event->context());
    }

    public function test_it_should_not_trigger_changes_when_no_change_of_state()
    {
	    $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'from', 'to')
            ->addSubscriber($subscriber = new TestSubscriber());

        $this->assertNull($subscriber->beforeEvent);
        $machine->transitContext('transition', TestContext::fromString('to'));
        $this->assertNull($subscriber->beforeEvent);
    }

    /**
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'transition' is not allowed on context 'context'.
     */
    public function test_it_should_throw_exception_when_transition_not_allowed()
    {
	    $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'to', 'to');

        $machine->transitContext('transition', TestContext::fromString());
    }

    public function test_state_can_have_attribute()
    {
	    $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'from', 'to')
            ->addAttribute('context', 'from', 'attribute')
        ;

        $this->assertTrue($machine->hasAttribute('attribute', TestContext::fromString('from')));
        $this->assertFalse($machine->hasAttribute('attribute', TestContext::fromString('to')));
    }
}

final class TestSubscriber implements EventSubscriber
{
    /**
     * @var TransitionWasRequested|null
     */
    public $beforeEvent;

    /**
     * @var TransitionWasSuccessful|null
     */
    public $afterEvent;

    /**
     * @var ContextTransitionWasRequested|null
     */
    public $beforeContextEvent;

    /**
     * @var ContextTransitionWasSuccessful|null
     */
    public $afterContextEvent;

    public function onBeforeTransition(TransitionWasRequested $event)
    {
        $this->beforeEvent = $event;
    }

    public function onAfterTransition(TransitionWasSuccessful $event)
    {
        $this->afterEvent = $event;
    }

    public function onBeforeContext(ContextTransitionWasRequested $event)
    {
        $this->beforeContextEvent = $event;
    }

    public function onAfterContext(ContextTransitionWasSuccessful $event)
    {
        $this->afterContextEvent = $event;
    }

    public static function getSubscribedEvents()
    {
        return [
            StateEventStore::BEFORE_TRANSITION => 'onBeforeTransition',
            StateEventStore::AFTER_TRANSITION => 'onAfterTransition',
            StateEventStore::preTransitionEvent('transition', 'context') => 'onBeforeContext',
            StateEventStore::postTransitionEvent('transition', 'context') => 'onAfterContext',
        ];
    }
}
