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

final class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The transition 'not-configured' could not be found for context 'context'.
     */
    public function test_it_should_not_allow_to_transition_to_a_not_configured_transition()
    {
        $machine = StateMachine::create();
        $machine->transitContext('not-configured', TestContext::fromString());
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The transition 'invalid' could not be found for context 'context'.
     * @depends test_it_should_not_allow_to_transition_to_a_not_configured_transition
     */
    public function test_it_should_allow_to_change_exception_type_when_transition_not_found()
    {
        $machine = StateMachine::create()
            ->useFailureHandler(new AlwaysThrowException('\RuntimeException'));

        $machine->transitContext('invalid', TestContext::fromString());
    }

    public function test_it_should_transition_from_one_state_to_the_other()
    {
        $context = TestContext::fromString();
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'from', 'to')
        ;

        $this->assertSame('from', $context->getCurrentState()->name());
        $machine->transitContext('transition', $context);
        $this->assertSame('to', $context->getCurrentState()->name());
    }

    public function test_it_should_trigger_an_event_before_any_transition()
    {
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
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'to', 'to');

        $machine->transitContext('transition', TestContext::fromString());
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The transition 'transition' is not allowed on context 'context'.
     * @depends test_it_should_throw_exception_when_transition_not_allowed
     */
    public function test_it_should_allow_to_change_exception_type_when_transition_not_allowed()
    {
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'to', 'to')
            ->useFailureHandler(new AlwaysThrowException(\RuntimeException::class));

        $machine->transitContext('transition', TestContext::fromString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The status was expected to be a string, 'integer' given.
     */
    public function test_it_should_throw_exception_when_not_supported_from_state_is_given()
    {
        $machine = StateMachine::create();
        $machine->oneToOne('test', 'name', 213, 'string');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The status was expected to be a string, 'integer' given.
     */
    public function test_it_should_throw_exception_when_not_supported_to_state_is_given()
    {
        $machine = StateMachine::create();
        $machine->oneToOne('test', 'name', 'string', 213);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The transition's name must be a string, got 'integer'.
     */
    public function test_it_should_throw_exception_when_not_supported_contet_name_is_given()
    {
        $machine = StateMachine::create();
        $machine->oneToOne('test', 213, 'string', 'string');
    }

    public function test_state_can_have_attribute()
    {
        $machine = StateMachine::create()
            ->oneToOne('context', 'transition', 'from', 'to')
            ->addAttribute('context', 'from', 'attribute')
        ;

        $this->assertTrue($machine->hasAttribute('attribute', TestContext::fromString('from')));
        $this->assertFalse($machine->hasAttribute('attribute', TestContext::fromString('to')));
    }

    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The state 'invalid' could not be found for context 'context'.
     */
    public function test_it_should_throw_exception_when_state_not_found()
    {
        StateMachine::create()->addAttribute('context', 'invalid', 'attribute');
    }

    public function test_it_should_allow_to_define_one_to_many_states_transition()
    {
        $machine = StateMachine::create()
            ->oneToMany('context', 'transition', 's1', ['s2', 's3'])
        ;
        $context = TestContext::fromString('s1');

        $this->assertTrue($machine->isState('s1', $context));
        $machine->transitContext('transition', $context);
        $this->assertTrue($machine->isState('s1', $context));
    }

    public function test_it_should_allow_to_define_many_to_many_states_transition()
    {
        $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->manyToMany('name', 'first', ['second', 'third', 'fourth'])
        ;
    }

    public function test_it_should_allow_to_disallow_transition()
    {
        $this->markTestIncomplete('TODO');
        $machine = StateMachine::create()
            ->disallow('name', 'first', ['second', 'third', 'fourth'])
        ;
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
