<?php

namespace Star\Component\State\States;

use PHPUnit\Framework\TestCase;
use Star\Component\State\State;

final class StringStateTest extends TestCase
{
    public function test_it_should_contains_unique_attribute()
    {
        $state = new StringState('name', ['one', 'one', 'one']);
        $this->assertTrue($state->hasAttribute('one'));
        $this->assertFalse($state->hasAttribute('not'));
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Expected a string. Got: Mock_State_
     */
    public function test_it_should_throw_exception_when_not_string()
    {
        $state = new StringState('name');
        $this->assertFalse($state->matchState($this->getMock(State::class)));
    }

    public function test_it_should_check_match_with_name()
    {
        $state1 = new StringState('s1');
        $this->assertTrue($state1->matchState('s1'));
        $this->assertFalse($state1->matchState('s2'));
    }

    public function test_it_should_check_match_with_attributes()
    {
        $state1 = new StringState('s1');
        $state2 = new StringState('s1', ['attr-1']);
        $state3 = new StringState('s1', ['attr-2']);
        $state4 = new StringState('s1', ['attr-2', 'attr-1']);

        $this->assertTrue($state1->matchState('s1'));
        $this->assertTrue($state2->matchState('s1'));
        $this->assertTrue($state3->matchState('s1'));
        $this->assertTrue($state4->matchState('s1'));
    }
}
