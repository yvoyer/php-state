<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Event\ContextTransitionWasRequested;
use Star\Component\State\Event\ContextTransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasSuccessful;
use Star\Component\State\Event\TransitionWasRequested;
use Star\Component\State\Example\Post;
use Star\Component\State\Example\PostSubscriber;

final class StateMachineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The transition 'not-configured' could not be found for context 'post'.
     */
    public function test_it_should_not_allow_to_transition_to_a_not_configured_transition()
    {
        $context = Post::draft();
        $machine = Post::workflow($context);
        $machine->transitContext('not-configured', $context);
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The transition 'invalid' could not be found for context 'post'.
     * @depends test_it_should_not_allow_to_transition_to_a_not_configured_transition
     */
    public function test_it_should_allow_to_change_exception_type_when_transition_not_found()
    {
        $machine = Post::workflow()
            ->useFailureHandler(new AlwaysThrowException('\RuntimeException'));

        $machine->transitContext('invalid', Post::draft());
    }

    public function test_it_should_transition_from_one_state_to_the_other()
    {
        $context = Post::draft();
        $machine = Post::workflow($context);

        $this->assertSame(Post::DRAFT, $context->getCurrentState()->name());
        $machine->transitContext(Post::TRANSITION_PUBLISH, $context);
        $this->assertSame(Post::PUBLISHED, $context->getCurrentState()->name());
    }

    public function test_it_should_trigger_an_event_before_any_transition()
    {
        $subscriber = new PostSubscriber();
        $context = Post::draft();
        $machine = Post::workflow($context)
            ->addSubscriber($subscriber)
        ;
        $this->assertFalse($subscriber->beforeTransition);

        $machine->transitContext(Post::TRANSITION_PUBLISH, $context);

        $event = $subscriber->beforeTransition;
        $this->assertInstanceOf(TransitionWasRequested::class, $event);
        $this->assertInstanceOf(StateTransition::class, $event->transition());
    }

    public function test_it_should_trigger_an_event_after_any_transition()
    {
        $subscriber = new PostSubscriber();
        $context = Post::draft();
        $machine = Post::workflow($context)
            ->addSubscriber($subscriber)
        ;
        $this->assertFalse($subscriber->afterTransition);

        $machine->transitContext(Post::TRANSITION_PUBLISH, $context);

        $event = $subscriber->afterTransition;
        $this->assertInstanceOf(TransitionWasSuccessful::class, $event);
        $this->assertInstanceOf(StateTransition::class, $event->transition());
    }

    public function test_it_should_trigger_a_custom_event_before_a_specific_transition()
    {
        $subscriber = new PostSubscriber();
        $context = Post::draft();
        $machine = Post::workflow($context)
            ->addSubscriber($subscriber)
        ;
        $this->assertFalse($subscriber->preSpecificTransition);

        $machine->transitContext(Post::TRANSITION_PUBLISH, $context);

        $event = $subscriber->preSpecificTransition;
        $this->assertInstanceOf(ContextTransitionWasRequested::class, $event);
        $this->assertEquals($context, $event->context());
    }

    public function test_it_should_trigger_a_custom_event_after_a_specific_transition()
    {
        $subscriber = new PostSubscriber();
        $context = Post::draft();
        $machine = Post::workflow($context)
            ->addSubscriber($subscriber)
        ;
        $this->assertFalse($subscriber->postSpecificTransition);

        $machine->transitContext(Post::TRANSITION_PUBLISH, $context);

        $event = $subscriber->postSpecificTransition;
        $this->assertInstanceOf(ContextTransitionWasSuccessful::class, $event);
        $this->assertEquals($context, $event->context());
    }

    public function test_it_should_not_trigger_changes_when_no_change_of_state()
    {
        $subscriber = new PostSubscriber();
        $context = Post::published();
        $machine = Post::workflow($context)
            ->addSubscriber($subscriber);

        $this->assertFalse($subscriber->beforeTransition);
        $machine->transitContext(Post::TRANSITION_PUBLISH, $context);
        $this->assertFalse($subscriber->beforeTransition);
    }

    /**
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'publish' is not allowed on context 'post'.
     */
    public function test_it_should_throw_exception_when_transition_not_allowed()
    {
        $machine = Post::workflow()
            ->useFailureHandler(new AlwaysThrowException());

        $machine->transitContext('publish', Post::deleted());
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage The transition 'publish' is not allowed on context 'post'.
     * @depends test_it_should_throw_exception_when_transition_not_allowed
     */
    public function test_it_should_allow_to_change_exception_type_when_transition_not_allowed()
    {
        $machine = Post::workflow()
            ->useFailureHandler(new AlwaysThrowException(\RuntimeException::class));

        $machine->transitContext('publish', Post::deleted());
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
        $machine = Post::workflow()
            ->addAttribute(Post::ALIAS, Post::PUBLISHED, Post::ATTRIBUTE_ACTIVE)
        ;

        $this->assertTrue($machine->hasAttribute(Post::ATTRIBUTE_ACTIVE, Post::published()));
        $this->assertFalse($machine->hasAttribute(Post::ATTRIBUTE_ACTIVE, Post::deleted()));
    }

    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The state 'invalid' could not be found for context 'post'.
     */
    public function test_it_should_throw_exception_when_state_not_found()
    {
        Post::workflow()->addAttribute(Post::ALIAS, 'invalid', 'attribute');
    }

    public function test_it_should_allow_to_define_one_to_many_states_transition()
    {
        $this->markTestIncomplete('TODO');
        $machine = Post::workflow()
            ->oneToMany('name', 'first', ['second', 'third', 'fourth'])
        ;
    }

    public function test_it_should_allow_to_define_many_to_many_states_transition()
    {
        $this->markTestIncomplete('TODO');
        $machine = Post::workflow()
            ->manyToMany('name', 'first', ['second', 'third', 'fourth'])
        ;
    }

    public function test_it_should_allow_to_disallow_transition()
    {
        $this->markTestIncomplete('TODO');
        $machine = Post::workflow()
            ->disallow('name', 'first', ['second', 'third', 'fourth'])
        ;
    }
}
