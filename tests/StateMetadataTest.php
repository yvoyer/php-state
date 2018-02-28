<?php

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Transitions\ClosureCallback;

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
            CustomMetadata::class,
            $new = $metadata->transit('t1', 'context')
        );
        $this->assertTrue($new->isInState('to'));
    }

    public function test_it_should_use_the_failure_callback_on_transit() {
        $metadata = new CustomMetadata('to');
        $this->setExpectedException(\RuntimeException::class, 'Callback was called');
        $metadata->transit(
            't1',
            'context',
            new ClosureCallback(
                function () {
                    throw new \RuntimeException('Callback was called');
                }
            )
        );
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
