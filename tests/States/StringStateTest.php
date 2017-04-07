<?php

namespace Star\Component\State\States;

use Star\Component\State\State;

final class StringStateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringState
     */
    private $state;

    public function setUp()
    {
        $this->state = new StringState('one', ['attribute_1', 'attribute_2']);
    }

    public function test_it_should_contains_unique_attribute()
    {
        $state = new StringState('name', ['one', 'one', 'one']);
        $this->assertTrue($state->hasAttribute('one'));
        $this->assertFalse($state->hasAttribute('not'));
    }

    public function test_it_should_not_match_state_when_different_class_type()
    {
        $state = new StringState('name', ['one', 'one', 'one']);
        $this->assertTrue($state->hasAttribute('one'));
        $this->assertFalse($state->hasAttribute('not'));

        $this->assertFalse($state->matchState($this->getMock(State::class)));
    }
}
