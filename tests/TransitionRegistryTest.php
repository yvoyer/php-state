<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\States\StringState;
use Star\Component\State\Transitions\ManyToOneTransition;
use Star\Component\State\Transitions\OneToOneTransition;

final class TransitionRegistryTest extends TestCase
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
            new OneToOneTransition(
                'name',
                new StringState('from'),
                new StringState('to')
            )
        );
        $transition = $this->registry->getTransition('name');
        $this->assertInstanceOf(StateTransition::class, $transition);
        $this->assertSame('name', $transition->getName());
    }

    public function test_it_should_contain_the_states() {
        $this->registry->addState(new StringState('from'));
        $this->assertEquals(new StringState('from'), $this->registry->getState('from'));
    }

    public function test_it_should_merge_attributes_when_duplicate_state_is_registered()
    {
        $this->registry->addState(new StringState('from'));
        $this->assertFalse($this->registry->getState('from')->hasAttribute('attr'));
        $this->assertFalse($this->registry->getState('from')->hasAttribute('other'));

        $this->registry->addState(new StringState('from', ['attr']));

        $this->assertTrue($this->registry->getState('from')->hasAttribute('attr'));
        $this->assertFalse($this->registry->getState('from')->hasAttribute('other'));

        $this->registry->addState(new StringState('from', ['other']));

        $this->assertTrue($this->registry->getState('from')->hasAttribute('attr'));
        $this->assertTrue($this->registry->getState('from')->hasAttribute('other'));
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
            new OneToOneTransition(
                'duplicate',
                new StringState('from'),
                new StringState('to')
            )
        );
        $this->registry->addTransition(
            new OneToOneTransition(
                'duplicate',
                new StringState('from'),
                new StringState('to')
            )
        );
    }

    public function test_it_should_register_multiple_state_when_transition_has_multiple_source_state()
    {
        $this->registry->addTransition(
            new ManyToOneTransition(
                'name',
                [
                    new StringState('from1'),
                    new StringState('from2'),
                ],
                new StringState('to')
            )
        );
        $this->assertInstanceOf(State::class, $this->registry->getState('from1'));
        $this->assertInstanceOf(State::class, $this->registry->getState('from2'));
        $this->assertInstanceOf(State::class, $this->registry->getState('to'));
    }
}
