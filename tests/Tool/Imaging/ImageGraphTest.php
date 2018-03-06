<?php

namespace Star\Component\State\Tool\Imaging;

use PHPUnit\Framework\TestCase;

final class ImageGraphTest extends TestCase
{
    public function test_it_should_do_something()
    {
        $graph = new ImageGraph(['s1', 's2', 's3']);
        $this->assertCount(3, $graph->getStates());
        $this->assertContainsOnlyInstancesOf(ImagingState::class, $graph->getStates());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage The state 's1' do not exists in the graph.
     */
    public function test_it_should_throw_exception_when_state_do_not_exists()
    {
        $graph = new ImageGraph([]);
        $graph->addTransition('name', 's1', 's2');
    }
}
