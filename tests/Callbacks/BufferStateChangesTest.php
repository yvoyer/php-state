<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Context\ObjectAdapterContext;
use Star\Component\State\Context\StringAdapterContext;

final class BufferStateChangesTest extends TestCase
{
    public function test_it_should_buffer_context_as_object(): void
    {
        $buffer = new BufferStateChanges();
        $machine = StateBuilder::build()
            ->create('');

        $buffer->beforeStateChange(
            new ObjectAdapterContext((object)[]),
            $machine
        );
        $buffer->afterStateChange(
            new ObjectAdapterContext((object)[]),
            $machine
        );

        self::assertSame(
            [
                'stdClass' => [
                    0 => 'beforeStateChange',
                    1 => 'afterStateChange',
                ],
            ],
            $buffer->flushBuffer(),
        );
    }

    public function test_it_should_buffer_context_as_string(): void
    {
        $buffer = new BufferStateChanges();
        $machine = StateBuilder::build()
            ->create('');

        $buffer->beforeStateChange(
            new StringAdapterContext('stdClass'),
            $machine
        );
        $buffer->afterStateChange(
            new StringAdapterContext('stdClass'),
            $machine
        );

        self::assertSame(
            [
                'stdClass' => [
                    0 => 'beforeStateChange',
                    1 => 'afterStateChange',
                ],
            ],
            $buffer->flushBuffer(),
        );
    }
}
