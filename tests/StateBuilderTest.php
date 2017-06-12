<?php

namespace Star\Component\State;

use Star\Component\State\Builder\StateBuilder;

final class StateBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_throw_exception_when_current_state_not_a_string()
    {
        $this->setExpectedException(
            \InvalidArgumentException::class,
            "The current state name was expected to be a string. Got: NULL."
        );
        StateBuilder::build()->create(null);
    }
}
