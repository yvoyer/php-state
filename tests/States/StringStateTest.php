<?php

namespace Star\Component\State\States;

use Star\Component\State\State;

final class StringStateTest extends \PHPUnit_Framework_TestCase
{
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

    public function test_it_should_check_match_with_name()
    {
        $state1 = new StringState('s1');
        $state2 = new StringState('s2');
        $this->assertTrue($state1->matchState($state1));
        $this->assertFalse($state1->matchState($state2));
    }

    public function test_it_should_check_match_with_attributes()
    {
        $state1 = new StringState('s1', ['attr-1']);
        $state2 = new StringState('s1', ['attr-2']);
        $state3 = new StringState('s1', ['attr-2', 'attr-1']);

        $this->assertTrue($state1->matchState($state1));
        $this->assertFalse($state1->matchState($state2));
        $this->assertFalse($state1->matchState($state3));
        $this->assertFalse($state2->matchState($state1));
        $this->assertTrue($state2->matchState($state2));
        $this->assertFalse($state2->matchState($state3));
        $this->assertFalse($state3->matchState($state1));
        $this->assertFalse($state3->matchState($state2));
        $this->assertTrue($state3->matchState($state3));
    }
}
