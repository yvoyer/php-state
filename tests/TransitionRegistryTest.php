<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\States\StringState;
use Star\Component\State\Transitions\AllowedTransition;

final class TransitionRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TransitionRegistry
     */
    private $registry;

    public function setUp()
    {
        $this->registry = new TransitionRegistry();
    }

    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The transition 'not-found' could not be found.
     */
    public function test_it_should_throw_exception_when_transition_is_not_registered()
    {
        $this->registry->getTransition('not-found');
    }

    /**
     * @expectedException        \Star\Component\State\NotFoundException
     * @expectedExceptionMessage The state 'not-found' could not be found.
     */
    public function test_it_should_throw_exception_when_state_is_not_registered()
    {
        $this->registry->getState('not-found');
    }

    public function test_it_should_add_transition()
    {
        $this->registry->addTransition(
            new AllowedTransition(
                'name',
                new StringState('from'),
                new StringState('to')
            )
        );
        $transition = $this->registry->getTransition('name');
        $this->assertInstanceOf(StateTransition::class, $transition);
        $this->assertSame('name', $transition->name());
    }

    public function test_it_should_contain_the_states() {
        $this->registry->addState(new StringState('from'));
        $this->assertEquals(new StringState('from'), $this->registry->getState('from'));
    }

    /**
     * @expectedException        \Star\Component\State\DuplicateEntryException
     * @expectedExceptionMessage The state "from" is already registered, maybe there is a mismatch with the attributes.
     */
    public function test_it_should_throw_exception_when_duplicate_state_is_registered()
    {
        $stateOne = new StringState('from');
        $stateTwo = new StringState('from', ['attr' => 'val']);
        $this->assertFalse($stateOne->matchState($stateTwo));
        $this->registry->addState($stateOne);
        $this->registry->addState($stateTwo);
    }

    public function test_it_should_not_generate_error_when_state_is_same()
    {
        $stateOne = new StringState('from');
        $stateTwo = new StringState('from');
        $this->assertTrue($stateOne->matchState($stateTwo));
        $this->registry->addState($stateOne);
        $this->registry->addState($stateTwo);
    }

    /**
     * @expectedException        \Star\Component\State\DuplicateEntryException
     * @expectedExceptionMessage The transition 'duplicate' is already registered.
     */
    public function test_it_should_throw_exception_when_duplicate_transition_is_registered()
    {
        $this->registry->addTransition(
            new AllowedTransition(
                'duplicate',
                new StringState('from'),
                new StringState('to')
            )
        );
        $this->registry->addTransition(
            new AllowedTransition(
                'duplicate',
                new StringState('from'),
                new StringState('to')
            )
        );
    }
}
