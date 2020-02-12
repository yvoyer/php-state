<?php declare(strict_types=1);

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Callbacks\NullCallback;

final class StateMetadataTest extends TestCase
{
    public function test_it_should_check_if_current_state_is_same(): void
    {
        $metadata = new CustomMetadata('from');
        $this->assertTrue($metadata->isInState('from'));
        $this->assertFalse($metadata->isInState('to'));
    }

    public function test_it_should_check_if_has_attribute(): void
    {
        $metadata = new CustomMetadata('from');
        $this->assertFalse($metadata->hasAttribute('attr'));
    }

    public function test_it_should_transit(): void
    {
        $metadata = new CustomMetadata('from');
        $this->assertInstanceOf(
            CustomMetadata::class,
            $new = $metadata->transit('t1', 'context')
        );
        $this->assertTrue($new->isInState('to'));
    }

    public function test_it_should_use_the_failure_callback_on_transit(): void
    {
        $metadata = new CustomMetadata('to');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage(
            'Method Star\Component\State\Callbacks\NullCallback::onFailure should never be called.'
        );
        $metadata->transit(
            't1',
            'context',
            new NullCallback()
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
    protected function configure(StateBuilder $builder): void
    {
        $builder->allowTransition('t1', 'from', 'to');
    }
}
