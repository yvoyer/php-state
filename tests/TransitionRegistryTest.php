<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Transitions\ManyToOneTransition;
use Star\Component\State\Transitions\OneToOneTransition;

final class TransitionRegistryTest extends TestCase
{
    private TransitionRegistry $registry;

    public function setUp(): void
    {
        $this->registry = new TransitionRegistry();
    }

    public function test_it_should_throw_exception_when_transition_is_not_registered(): void
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage("The transition 'not-found' could not be found.");
        $this->registry->getTransition('not-found');
    }

    public function test_it_should_throw_exception_when_state_is_not_registered(): void
    {
        self::assertFalse($this->registry->hasState('not-found'));
    }

    public function test_it_should_contain_the_states(): void
    {
        self::assertFalse($this->registry->hasState('from'));
        $this->registry->registerStartingState('t', 'from');
        self::assertTrue($this->registry->hasState('from'));
    }

    public function test_it_should_merge_attributes_when_duplicate_state_is_registered(): void
    {
        $this->registry->registerStartingState('t1', 'from');
        self::assertFalse($this->registry->hasAttribute('from', 'attr'));
        self::assertFalse($this->registry->hasAttribute('from', 'other'));

        $this->registry->registerStartingState('t2', 'from', ['attr']);

        self::assertTrue($this->registry->hasAttribute('from', 'attr'));
        self::assertFalse($this->registry->hasAttribute('from', 'other'));

        $this->registry->registerStartingState('t3', 'from', ['other']);

        self::assertTrue($this->registry->hasAttribute('from', 'attr'));
        self::assertTrue($this->registry->hasAttribute('from', 'other'));
    }

    public function test_it_should_throw_exception_when_duplicate_transition_is_registered(): void
    {
        $this->registry->addTransition(
            new OneToOneTransition('duplicate', 'from', 'to')
        );

        $this->expectException(DuplicateEntryException::class);
        $this->expectExceptionMessage("The transition 'duplicate' is already registered.");
        $this->registry->addTransition(
            new OneToOneTransition('duplicate', 'from', 'to')
        );
    }

    public function test_it_should_register_multiple_state_when_transition_has_multiple_source_state(): void
    {
        $this->registry->addTransition(
            new ManyToOneTransition('name', 'to', 'from1', 'from2')
        );
        self::assertTrue($this->registry->hasState('from1'));
        self::assertTrue($this->registry->hasState('from2'));
        self::assertTrue($this->registry->hasState('to'));
    }

    public function test_it_should_return_if_the_state_exists(): void
    {
        $this->registry->registerStartingState('t', 'exists');
        self::assertTrue($this->registry->hasState('exists'));
        self::assertFalse($this->registry->hasState('not-exists'));
    }

    public function test_it_should_return_whether_the_transition_starts_from_a_state(): void
    {
        $this->registry->addTransition(new OneToOneTransition('t', 'from', 'to'));

        self::assertTrue($this->registry->transitionStartsFrom('t', 'from'));
        self::assertFalse($this->registry->transitionStartsFrom('t', 'to'));
    }

    public function test_it_should_visit_the_transitions(): void
    {
        $visitor = $this->getMockBuilder(TransitionVisitor::class)->getMock();
        $visitor
            ->expects($this->once())
            ->method('visitTransition')
            ->with('t');
        $visitor
            ->expects($this->once())
            ->method('visitFromState')
            ->with('from', ['attr-1']);
        $visitor
            ->expects($this->once())
            ->method('visitToState')
            ->with('to', ['attr-2']);

        $this->registry->registerStartingState('t', 'from', ['attr-1']);
        $this->registry->registerDestinationState('t', 'to', ['attr-2']);
        $this->registry->acceptTransitionVisitor($visitor);
    }

    public function test_it_should_visit_the_states(): void
    {
        $visitor = new class implements StateVisitor
        {
            /**
             * @var array<string, array<int, string[]>>
             */
            public array $attributes = [];

            public function visitState(string $name, array $attributes): void
            {
                $this->attributes[$name][] = $attributes;
            }
        };
        $this->registry->addTransition(new OneToOneTransition('t', 'from', 'to'));
        $this->registry->addAttribute('from', 'attr');

        $this->registry->acceptStateVisitor($visitor);
        self::assertSame(
            [
                'from' => [
                    0 => [
                        0 => 'attr',
                    ],
                ],
                'to' => [
                    0 => [],
                ],
            ],
            $visitor->attributes
        );
    }
}
