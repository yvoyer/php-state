<?php declare(strict_types=1);

namespace Star\Component\State\Event;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class StateEventStoreTest extends TestCase
{
    public function test_it_should_throw_exception_when_invalid_event(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('is not mapped to a name.');
        StateEventStore::eventNameFromClass($this->createStub(StateEvent::class));
    }
}
