<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

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
     * @expectedExceptionMessage The transition 'not-found' could not be found for context 'context'.
     */
    public function test_it_should_throw_exception_when_transition_is_not_registed()
    {
        $this->registry->getTransition('not-found', 'context');
    }

    public function test_it_should_add_transition()
    {
        $this->registry->addTransition(
            'context',
            new OneToOneTransition('name', new StringState('from'), new StringState('to'))
        );
        $transition = $this->registry->getTransition('name', 'context');
        $this->assertInstanceOf(StateTransition::class, $transition);
        $this->assertSame('name', $transition->name());
    }

    public function test_it_should_contain_the_states() {
        $this->registry->addTransition(
            'context',
            new OneToOneTransition('name', new StringState('from'), new StringState('to'))
        );
        $this->assertEquals(new StringState('from'), $this->registry->getState('from', 'context'));
        $this->assertEquals(new StringState('to'), $this->registry->getState('to', 'context'));
    }

//    /**
//     * @expectedException        \Star\Component\State\NotFoundException
//     * @expectedExceptionMessage The transition 'not-found' could not be found for context 'context'.
//     */
    public function test_it_should_throw_exception_when_duplicate_state_is_not_equal_but_it_has_same_name()
    {
        $this->markTestIncomplete('TODO');
    }
}
