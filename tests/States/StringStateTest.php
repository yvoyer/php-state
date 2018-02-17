<?php

namespace Star\Component\State\States;

use PHPUnit\Framework\TestCase;

final class StringStateTest extends TestCase
{
    public function test_it_should_contains_unique_attribute()
    {
        $state = new StringState('name', ['one', 'one', 'one']);
        $this->assertTrue($state->hasAttribute('one'));
        $this->assertFalse($state->hasAttribute('not'));
    }
}
