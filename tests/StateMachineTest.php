<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Events\TransitionWasPerformed;
use Star\Component\State\Events\TransitionWasRequested;

final class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \Star\Component\State\InvalidGameTransitionException
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

    public function test_it_should_trigger_a_callback_on_before_transition()
    {
        $triggered = false;
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist(
                'start',
                'event',
                function (TransitionWasRequested $event) use (&$triggered) {
                    $triggered = true;
                }
            )
        ;

        $this->assertTrue($machine->isAllowed('start', 'event'));

        $machine->transitContext($context, 'event');
        $this->assertTrue($triggered, 'The Callback should be triggered on transition');
    }

    public function test_it_should_trigger_a_callback_on_after_transition()
    {
        $triggered = false;
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist(
                'start',
                'event',
                null,
                function (TransitionWasPerformed $event) use (&$triggered) {
                    $triggered = true;
                }
            )
        ;

        $this->assertTrue($machine->isAllowed('start', 'event'));

        $machine->transitContext($context, 'event');
        $this->assertTrue($triggered, 'The Callback should be triggered on transition');
    }

    public function test_it_should_trigger_a_custom_event_before_a_transition()
    {
        $subscriber = new TestSubscriber();

        $this->assertFalse($subscriber->methodWasCalled('beforeStartToFinish'));
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'finish')
            ->addSubscriber($subscriber)
        ;
        $this->assertTrue($machine->isAllowed('start', 'finish'));

        $machine->transitContext($context, 'finish');

        $this->assertTrue(
            $subscriber->methodWasCalled('beforeStartToFinish'),
            'The start to finish transition event should be triggered on before'
        );
    }

    public function test_it_should_trigger_a_custom_event_after_a_transition()
    {
        $subscriber = new TestSubscriber();

        $this->assertFalse($subscriber->methodWasCalled('afterStartToFinish'));
        $context = TestContext::fromString('start');
        $machine = StateMachine::create($context)
            ->whitelist('start', 'finish')
            ->addSubscriber($subscriber)
        ;
        $this->assertTrue($machine->isAllowed('start', 'finish'));

        $machine->transitContext($context, 'finish');

        $this->assertTrue(
            $subscriber->methodWasCalled('afterStartToFinish'),
            'The start to finish transition event should be triggered on after'
        );
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

        $this->assertFalse($subscriber->methodWasCalled('shouldNotBeCalled'));
        $machine = StateMachine::create($context)
            ->whitelist('first', 'first')
            ->addSubscriber($subscriber);
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

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            'before.context.start_to_finish' => 'beforeStartToFinish',
            'after.context.start_to_finish' => 'afterStartToFinish',
            'before.context.first_to_first' => 'shouldNotBeCalled',
            'after.context.first_to_first' => 'shouldNotBeCalled',
        ];
    }

    public function afterStartToFinish()
    {
        $this->methods[__FUNCTION__] = 1;
    }

    public function beforeStartToFinish()
    {
        $this->methods[__FUNCTION__] = 1;
    }

    public function shouldNotBeCalled(TransitionWasRequested $event)
    {
        $this->methods[__FUNCTION__] = 1;
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
