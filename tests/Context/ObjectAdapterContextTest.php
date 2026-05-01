<?php declare(strict_types=1);

namespace Star\Component\State\Context;

use PHPUnit\Framework\TestCase;
use stdClass;

final class ObjectAdapterContextTest extends TestCase
{
    public function test_it_should_return_context_of_class_from_global_namespace(): void
    {
        self::assertSame(
            'stdClass',
            (new ObjectAdapterContext(new stdClass()))->toStateContextIdentifier(),
        );
    }

    public function test_it_should_return_context_of_class_from_namespace(): void
    {
        self::assertSame(
            'HardCodedTestContext',
            (new ObjectAdapterContext(new HardCodedTestContext()))->toStateContextIdentifier(),
        );
    }
}
