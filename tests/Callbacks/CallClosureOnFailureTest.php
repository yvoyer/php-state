<?php declare(strict_types=1);

namespace Star\Component\State\Callbacks;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Star\Component\State\EventRegistry;
use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;
use Star\Component\State\StateRegistry;

final class CallClosureOnFailureTest extends TestCase
{
    public function test_it_should_throw_exception_when_callback_do_not_return_string(): void
    {
        $handler = new CallClosureOnFailure(
            function () {
                return 123;
            }
        );
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Callback should be returning a string, type "integer" returned.');
        $handler->onFailure(
            new InvalidStateTransitionException(),
            'context',
            new StateMachine(
                'state',
                $this->createMock(StateRegistry::class),
                $this->createMock(EventRegistry::class),
            )
        );
    }
}
