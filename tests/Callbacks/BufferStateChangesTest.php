<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;

class BufferStateChangesTest extends TestCase
{
    public function test_it_should_buffer_context_as_object(): void
    {
        $buffer = new BufferStateChanges();
        $machine = StateBuilder::build()
            ->create('');

        $buffer->beforeStateChange(
            (object) [],
            $machine
        );
        $buffer->afterStateChange(
            (object) [],
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
            'stdClass',
            $machine
        );
        $buffer->afterStateChange(
            'stdClass',
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

    public function test_it_should_not_allow_non_string_context_in_before(): void
    {
        $buffer = new BufferStateChanges();
        $machine = StateBuilder::build()
            ->create('');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Context is expected to be a string. Got: integer');
        $buffer->beforeStateChange(42, $machine);
    }

    public function test_it_should_not_allow_non_string_context_in_after(): void
    {
        $buffer = new BufferStateChanges();
        $machine = StateBuilder::build()
            ->create('');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Context is expected to be a string. Got: integer');
        $buffer->afterStateChange(42, $machine);
    }
}
