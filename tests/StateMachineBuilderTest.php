<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Example\Post;

final class StateMachineBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_set_the_post_initial_state()
    {
        $machine = StateMachineBuilder::create('post')
            ->registerState(Post::DRAFT)
            ->getMachine(Post::DRAFT);

        $this->assertSame('', $machine->getMarking(Post::archived()));
    }
}
