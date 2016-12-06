<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Attribute\StringAttribute;
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
        $context = TestContext::fromString('current');
        $machine = StateMachine::create($context);

        $machine->transitContext($context, 'not-configured');
    }

    public function test_it_should_transition_to_the_configured_state()
    {
        $context = TestContext::fromString('started');
        $machine = StateMachine::create($context)
            ->addTransition('finish', 'started', 'finished');

        $this->assertEquals(new StringState('started'), $context->getCurrentState());
        $machine->transitContext($context, 'finish');
        $this->assertEquals(new StringState('finished'), $context->getCurrentState());
    }

    public function test_it_should_trigger_an_event_before_any_transition()
    {
        $subscriber = new TestSubscriber();
        $context = TestContext::fromString('started');
        $machine = StateMachine::create($context)
            ->addTransition('finish', 'started', 'finished')
            ->addSubscriber($subscriber)
        ;

        $this->assertFalse($subscriber->methodWasCalled('beforeTransition'));
        $machine->transitContext($context, 'finish');
        $this->assertTrue($subscriber->methodWasCalled('beforeTransition'));

        /**
         * @var TransitionWasRequested $event
         */
        $event = $subscriber->triggeredEvent('beforeTransition');
        $this->assertInstanceOf(TransitionWasRequested::class, $event);
        $this->assertInstanceOf(StateTransition::class, $event->transition());
    }

    public function test_it_should_trigger_an_event_after_any_transition()
    {
        $subscriber = new TestSubscriber();
        $context = TestContext::fromString('started');
        $machine = StateMachine::create($context)
            ->addTransition('finish', 'started', 'finished')
            ->addSubscriber($subscriber)
        ;

        $this->assertFalse($subscriber->methodWasCalled('afterTransition'));
        $machine->transitContext($context, 'finish');
        $this->assertTrue($subscriber->methodWasCalled('afterTransition'));

        /**
         * @var TransitionWasSuccessful $event
         */
        $event = $subscriber->triggeredEvent('afterTransition');
        $this->assertInstanceOf(TransitionWasSuccessful::class, $event);
        $this->assertInstanceOf(StateTransition::class, $event->transition());
    }

    public function test_it_should_trigger_a_custom_event_before_a_specific_transition()
    {
        $subscriber = new TestSubscriber();
        $context = TestContext::fromString('started');
        $machine = StateMachine::create($context)
            ->addSubscriber($subscriber)
            ->addTransition('finish', 'started', 'finished');

        $this->assertFalse($subscriber->methodWasCalled('beforeStartToFinish'));
        $machine->transitContext($context, 'finish');
        $this->assertTrue(
            $subscriber->methodWasCalled('beforeStartToFinish'),
            'The start to finish transition event should be triggered on before'
        );

        /**
         * @var ContextTransitionWasRequested $event
         */
        $event = $subscriber->triggeredEvent('beforeStartToFinish');
        $this->assertInstanceOf(ContextTransitionWasRequested::class, $event);
        $this->assertSame($context, $event->context());
    }

    public function test_it_should_trigger_a_custom_event_after_a_specific_transition()
    {
        $subscriber = new TestSubscriber();
        $context = TestContext::fromString('started');
        $machine = StateMachine::create($context)
            ->addTransition('finish', 'started', 'finished')
            ->addSubscriber($subscriber)
        ;

        $this->assertFalse($subscriber->methodWasCalled('afterStartToFinish'));
        $machine->transitContext($context, 'finish');
        $this->assertTrue(
            $subscriber->methodWasCalled('afterStartToFinish'),
            'The start to finish transition event should be triggered on after'
        );

        /**
         * @var ContextTransitionWasSuccessful $event
         */
        $event = $subscriber->triggeredEvent('afterStartToFinish');
        $this->assertInstanceOf(ContextTransitionWasSuccessful::class, $event);
        $this->assertSame($context, $event->context());
    }

    public function test_it_should_allow_to_give_array_for_white_listing_transitions()
    {
        $this->markTestIncomplete('TODO');
        $context = TestContext::fromString('first');
        $machine = StateMachine::create($context)
            ->addTransition('name', 'first', ['second', 'third', 'fourth'])
        ;

        $this->assertTrue($machine->isAllowed('first', 'second'));
        $this->assertTrue($machine->isAllowed('first', 'third'));
        $this->assertTrue($machine->isAllowed('first', 'fourth'));
    }

    public function test_it_should_not_trigger_changes_when_no_change_of_state()
    {
        $subscriber = new TestSubscriber();
        $context = TestContext::fromString('first');
        $machine = StateMachine::create($context)
            ->addTransition('no-change', 'first', 'first')
            ->addSubscriber($subscriber);

        $this->assertFalse($subscriber->methodWasCalled('shouldNotBeCalled'));
        $machine->transitContext($context, 'no-change');
        $this->assertFalse(
            $subscriber->methodWasCalled('shouldNotBeCalled'),
            'No events should have been triggered, since the state was not changed'
        );
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The transition from 'first' to 'invalid' is not allowed.
     */
    public function test_it_should_allow_to_change_exception_type_that_throw_exception()
    {
        $this->markTestIncomplete('TODO');
        $machine = StateMachine::create($context = TestContext::fromString('first'))
            ->useFailureHandler(new AlwaysThrowException('\RuntimeException'));

        $machine->transitContext($context, 'invalid');
    }

    public function test_it_should_allow_to_change_way_errors_are_handled()
    {
        $this->markTestIncomplete('TODO');
        $machine = StateMachine::create($context = TestContext::fromString('first'))
            ->useFailureHandler($this->getMock(FailureHandler::class));

        $machine->transitContext($context, 'invalid');
        $this->assertSame('first', $context->getCurrentState()->name(), 'The exception should be silenced');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The state of type 'integer' is not yet supported.
     */
    public function test_it_should_throw_exception_when_not_supported_state_type_is_given()
    {
        $this->markTestIncomplete('TODO');
        StateMachine::state(213);
    }

    public function test_state_can_have_attribute()
    {
        $context = TestContext::fromString('started');
        $machine = StateMachine::create($context)
            ->addTransition('finish', 'started', 'ended')
            ->addAttribute('is_valid', 'started')
        ;

        $this->assertTrue($machine->hasAttribute('is_valid'));
        $this->assertFalse($machine->hasAttribute('is_valid'));
    }

    public function test_state_can_have_attributes()
    {
        $context = TestContext::fromString('started');
        $machine = StateMachine::create($context)
            ->addTransition('finish', 'started', 'ended')
            ->addAttribute('is_valid', ['started', 'ended'])
            ->addAttribute('is_ended', 'ended')
        ;

	    $this->assertSame('started', $context->getCurrentState()->name());
        $this->assertTrue($machine->hasAttribute('is_valid'));
        $this->assertFalse($machine->hasAttribute('is_ended'));
        $context->setState(new StringState('ended'));
	    $this->assertSame('ended', $context->getCurrentState()->name());
        $this->assertTrue($machine->hasAttribute('is_valid'));
        $this->assertTrue($machine->hasAttribute('is_ended'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The attribute 'invalid' is not supported by the state.
     */
    public function test_it_should_throw_exception_when_state_do_not_supports_the_attribute()
    {
        $this->markTestIncomplete('TODO');
        $context = TestContext::fromString('started');
        $machine = StateMachine::create($context)
            ->addTransition('finish', 'started', 'ended')
            ->addAttribute('is_valid', 'started')
        ;

        $this->assertTrue($machine->isState('started', $context));
        $machine->hasAttribute('invalid');
    }

    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The attribute 'invalid' is not supported by the state.
     */
    public function test_it_should_throw_exception_when_state_not_found()
    {
        $this->markTestIncomplete('TODO');
        StateMachine::create(TestContext::fromString('start'))
            ->addAttribute('attr', 'state');
    }
}

final class TestContext implements StateContext
{
    /**
     * @var string
     */
    private $current;

    /**
     * @param $initial
     */
    private function __construct(State $initial)
    {
        $this->current = $initial->name();
    }

    public function setState(State $state)
    {
        $this->current = $state->name();
    }

    public function getCurrentState()
    {
        return new StringState($this->current);
    }

    public static function fromString($state)
    {
        return new self(new StringState($state));
    }

    /**
     * @return string
     */
    public function contextAlias()
    {
        return 'context';
    }
}

final class TestSubscriber implements EventSubscriber
{
    private $methods = [];

    public function methodWasCalled($method)
    {
        return isset($this->methods[$method]);
    }

    public function triggeredEvent($method)
    {
        return $this->methods[$method];
    }

    public static function getSubscribedEvents()
    {
        return [
            StateEventStore::BEFORE_TRANSITION => 'beforeTransition',
            StateEventStore::AFTER_TRANSITION => 'afterTransition',
            'star_state.before.context.finish' => 'beforeStartToFinish',
            'star_state.after.context.finish' => 'afterStartToFinish',
            'star_state.before.context.no-change' => 'shouldNotBeCalled',
            'star_state.after.context.no-change' => 'shouldNotBeCalled',
        ];
    }

    public function afterStartToFinish($event)
    {
        $this->methods[__FUNCTION__] = $event;
    }

    public function beforeStartToFinish($event)
    {
        $this->methods[__FUNCTION__] = $event;
    }

    public function beforeTransition($event)
    {
        $this->methods[__FUNCTION__] = $event;
    }

    public function afterTransition($event)
    {
        $this->methods[__FUNCTION__] = $event;
    }

    public function shouldNotBeCalled($event)
    {
        $this->methods[__FUNCTION__] = $event;
    }
}
