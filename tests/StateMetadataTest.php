<?php

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;

final class StateMetadataTest extends TestCase
{
    public function test_it_should_check_if_current_state_is_same()
    {
        $metadata = new CustomMetadata('from');
        $this->assertTrue($metadata->isInState('from'));
        $this->assertFalse($metadata->isInState('to'));
    }

    public function test_it_should_check_if_has_attribute()
    {
        $metadata = new CustomMetadata('from');
        $this->assertFalse($metadata->hasAttribute('attr'));
    }

    public function test_it_should_transit()
    {
        $metadata = new CustomMetadata('from');
        $this->assertInstanceOf(
            CustomMetadata::class, $new = $metadata->transit('t1', 'context')
        );
        $this->assertTrue($new->isInState('to'));
    }
}

final class CustomMetadata extends StateMetadata
{
    /**
     * Returns the state workflow configuration.
     *
     * @param StateBuilder $builder
     */
    protected function configure(StateBuilder $builder)
    {
        $builder->allowTransition('t1', 'from', 'to');
    }
}
