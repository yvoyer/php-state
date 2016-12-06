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
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition from 'current' to 'not-configured' is not allowed.
     */
    public function test_it_should_not_allow_to_transition_to_a_not_configured_state()
    {
        $context = TestContext::fromString('current');
        $machine = StateMachine::create($context);

        $machine->transitContext($context, new StringState('not-configured'));
    }

    public function test_it_should_transition_to_the_whitelisted_state_using_state_object()
    {
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist(new StringState('start'), new StringState('finish'));

        $this->assertTrue($machine->isAllowed(new StringState('start'), new StringState('finish')));

        $this->assertEquals(new StringState('start'), $context->getCurrentState());

        $machine->transitContext($context, new StringState('finish'));
    }

    public function test_it_should_transition_to_the_whitelisted_state_using_string()
    {
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'finish');

        $this->assertTrue($machine->isAllowed('start', 'finish'));

        $this->assertEquals(new StringState('start'), $context->getCurrentState());

        $machine->transitContext($context, 'finish');
    }

    public function test_it_should_trigger_an_event_before_any_transition()
    {
        $subscriber = new TestSubscriber();
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'end')
            ->addSubscriber($subscriber)
        ;

        $this->assertFalse($subscriber->methodWasCalled('beforeTransition'));
        $machine->transitContext($context, 'end');
        $this->assertTrue($subscriber->methodWasCalled('beforeTransition'));

        /**
         * @var TransitionWasRequested $event
         */
        $event = $subscriber->triggeredEvent('beforeTransition');
        $this->assertInstanceOf(TransitionWasRequested::class, $event);
        $this->assertEquals(new StringState('start'), $event->from());
        $this->assertEquals(new StringState('end'), $event->to());
    }

    public function test_it_should_trigger_an_event_after_any_transition()
    {
        $subscriber = new TestSubscriber();
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'end')
            ->addSubscriber($subscriber)
        ;

        $this->assertFalse($subscriber->methodWasCalled('afterTransition'));
        $machine->transitContext($context, 'end');
        $this->assertTrue($subscriber->methodWasCalled('afterTransition'));

        /**
         * @var TransitionWasSuccessful $event
         */
        $event = $subscriber->triggeredEvent('afterTransition');
        $this->assertInstanceOf(TransitionWasSuccessful::class, $event);
        $this->assertEquals(new StringState('start'), $event->before());
        $this->assertEquals(new StringState('end'), $event->current());
    }

    public function test_it_should_trigger_a_custom_event_before_a_specific_transition()
    {
        $subscriber = new TestSubscriber();
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->addSubscriber($subscriber)
            ->whitelist('start', 'finish');

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
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'finish')
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
        $machine = StateMachine::create(TestContext::fromString('first'))
            ->whitelist('first', ['second', 'third', 'fourth'])
        ;

        $this->assertTrue($machine->isAllowed('first', 'second'));
        $this->assertTrue($machine->isAllowed('first', 'third'));
        $this->assertTrue($machine->isAllowed('first', 'fourth'));
    }

    public function test_it_should_not_trigger_changes_when_no_change_of_state()
    {
        $context = TestContext::fromString('first');
        $subscriber = new TestSubscriber();
        $machine = StateMachine::create($context)
            ->whitelist('first', 'first')
            ->addSubscriber($subscriber);

        $this->assertFalse($subscriber->methodWasCalled('shouldNotBeCalled'));
        $machine->transitContext($context, 'first');
        $this->assertFalse(
            $subscriber->methodWasCalled('shouldNotBeCalled'),
            'No events should have been triggered, since the state was not changed'
        );
    }

    /**
     * @depends test_it_should_allow_to_give_array_for_white_listing_transitions
     */
    public function test_it_should_change_not_trigger_event_when_state_is_not_changed()
    {
        $context = new CarContext();
        $machine = StateMachine::create($context)
            ->whitelist('parked', ['started', 'stopped'])
            ->whitelist('started', ['parked', 'stopped'])
            ->whitelist('stopped', ['parked', 'started'])
        ;

        $this->assertFalse($machine->isAllowed('parked', 'parked'));
        $this->assertTrue($machine->isAllowed('parked', 'started'));
        $this->assertTrue($machine->isAllowed('parked', 'stopped'));
        $this->assertTrue($machine->isAllowed('started', 'parked'));
        $this->assertFalse($machine->isAllowed('started', 'started'));
        $this->assertTrue($machine->isAllowed('started', 'stopped'));
        $this->assertTrue($machine->isAllowed('stopped', 'parked'));
        $this->assertTrue($machine->isAllowed('stopped', 'started'));
        $this->assertFalse($machine->isAllowed('stopped', 'stopped'));

        $context = new CarContext();
        $this->assertTrue($context->isParked());
        $this->assertFalse($context->isStarted());
        $this->assertFalse($context->isStopped());

        $machine->transitContext($context, 'parked');
        $this->assertTrue($context->isParked());
        $this->assertFalse($context->isStarted());
        $this->assertFalse($context->isStopped());

        $machine->transitContext($context, 'started');
        $this->assertFalse($context->isParked());
        $this->assertTrue($context->isStarted());
        $this->assertFalse($context->isStopped());

        $machine->transitContext($context, 'stopped');
        $this->assertFalse($context->isParked());
        $this->assertFalse($context->isStarted());
        $this->assertTrue($context->isStopped());

        $machine->transitContext($context, 'parked');
        $this->assertTrue($context->isParked());
        $this->assertFalse($context->isStarted());
        $this->assertFalse($context->isStopped());

        $machine->transitContext($context, 'started');
        $this->assertFalse($context->isParked());
        $this->assertTrue($context->isStarted());
        $this->assertFalse($context->isStopped());

        $machine->transitContext($context, 'stopped');
        $this->assertFalse($context->isParked());
        $this->assertFalse($context->isStarted());
        $this->assertTrue($context->isStopped());

        $machine->transitContext($context, 'parked');
        $this->assertTrue($context->isParked());
        $this->assertFalse($context->isStarted());
        $this->assertFalse($context->isStopped());

        $machine->transitContext($context, 'started');
        $this->assertFalse($context->isParked());
        $this->assertTrue($context->isStarted());
        $this->assertFalse($context->isStopped());

        $machine->transitContext($context, 'stopped');
        $this->assertFalse($context->isParked());
        $this->assertFalse($context->isStarted());
        $this->assertTrue($context->isStopped());
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The transition from 'first' to 'invalid' is not allowed.
     */
    public function test_it_should_allow_to_change_exception_type_that_throw_exception()
    {
        $machine = StateMachine::create($context = TestContext::fromString('first'))
            ->useFailureHandler(new AlwaysThrowException('\RuntimeException'));

        $machine->transitContext($context, 'invalid');
    }

    public function test_it_should_allow_to_change_way_errors_are_handled()
    {
        $machine = StateMachine::create($context = TestContext::fromString('first'))
            ->useFailureHandler($this->getMock(FailureHandler::class));

        $machine->transitContext($context, 'invalid');
        $this->assertSame('first', $context->getCurrentState()->toString(), 'The exception should be silenced');
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The state of type 'integer' is not yet supported.
     */
    public function test_it_should_throw_exception_when_not_supported_state_type_is_given()
    {
        StateMachine::state(213);
    }

    public function test_state_can_have_attribute()
    {
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'end')
            ->addAttribute(new StringAttribute('is_valid'), new StringState('start'))
        ;

        $this->assertTrue($machine->isState('start', $context));
        $this->assertTrue($machine->hasAttribute('is_valid'));
        $this->assertFalse($machine->hasAttribute('is_valid'));
    }

    public function test_state_can_have_attributes()
    {
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'end')
            ->addAttribute(new StringAttribute('is_valid'), ['start', new StringState('end')])
            ->addAttribute('is_ended', 'end')
        ;

        $this->assertTrue($machine->isState('start', $context));
        $this->assertTrue($machine->hasAttribute('is_valid'));
        $this->assertFalse($machine->hasAttribute('is_ended'));
        $context->setState(new StringState('end'));
        $this->assertTrue($machine->hasAttribute('is_valid'));
        $this->assertFalse($machine->hasAttribute('is_ended'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The attribute 'invalid' is not supported by the state.
     */
    public function test_it_should_throw_exception_when_state_do_not_supports_the_attribute()
    {
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'end')
            ->addAttribute('is_valid', 'start')
        ;

        $this->assertTrue($machine->isState('start', $context));
        $machine->hasAttribute('invalid');
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
        $this->current = $initial->toString();
    }

    public function setState(State $state)
    {
        $this->current = $state->toString();
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
            'star_state.before.context.start_to_finish' => 'beforeStartToFinish',
            'star_state.after.context.start_to_finish' => 'afterStartToFinish',
            'star_state.before.context.first_to_first' => 'shouldNotBeCalled',
            'star_state.after.context.first_to_first' => 'shouldNotBeCalled',
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

final class CarContext implements StateContext
{
    private $state = 'parked';

    public function setState(State $state)
    {
        $this->state = $state->toString();
    }

    public function getCurrentState()
    {
        return new StringState($this->state);
    }

    public function contextAlias()
    {
        return 'car';
    }

    /**
     * @return bool
     */
    public function isParked()
    {
        return $this->stateMachine()->isState(new StringState('parked'), $this);
    }

    /**
     * @return bool
     */
    public function isStarted()
    {
        return $this->stateMachine()->isState('started', $this);
    }

    /**
     * @return bool
     */
    public function isStopped()
    {
        return $this->stateMachine()->isState('stopped', $this);
    }

    /**
     * @return StateMachine
     */
    private function stateMachine()
    {
        return StateMachine::create($this)
            ->whitelist('parked', ['started', 'stopped'])
            ->whitelist(new StringState('started'), ['parked', 'stopped'])
            ->whitelist('stopped', [new StringState('parked'), 'started'])
        ;
    }
}
