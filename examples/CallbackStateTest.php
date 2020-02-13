<?php declare(strict_types=1);

namespace Star\Component\State\Example;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Callbacks\CallContextMethodOnFailure;
use Star\Component\State\Callbacks\CallClosureOnFailure;
use Star\Component\State\Callbacks\TransitionCallback;
use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateMachine;
use Star\Component\State\StateMetadata;
use Star\Component\State\StateTransition;

final class CallbackStateTest extends TestCase
{
    public function test_workflow(): void
    {
        $turnStill = new TurnStill();
        $this->assertTrue($turnStill->isLocked());
        $this->assertFalse($turnStill->inViolation());

        $this->assertSame(0, $turnStill->coins());
        $turnStill->pay(1);
        $this->assertSame(1, $turnStill->coins());

        $this->assertFalse($turnStill->isLocked());
        $this->assertFalse($turnStill->inViolation());

        $this->assertSame(1, $turnStill->coins());
        $turnStill->pass();
        $this->assertSame(1, $turnStill->coins());

        $this->assertTrue($turnStill->isLocked());
        $this->assertFalse($turnStill->inViolation());

        $this->assertSame(1, $turnStill->coins());
        $turnStill->pass();
        $this->assertSame(1, $turnStill->coins());

        $this->assertTrue($turnStill->isLocked());
        $this->assertTrue($turnStill->inViolation());

        $this->assertSame(1, $turnStill->coins());
        $turnStill->pay(1);
        $this->assertSame(1, $turnStill->coins());

        $this->assertTrue($turnStill->isLocked());
        $this->assertTrue($turnStill->inViolation());

        $this->assertSame(1, $turnStill->coins());
        $turnStill->reset();
        $this->assertSame(1, $turnStill->coins());

        $this->assertTrue($turnStill->isLocked());
        $this->assertFalse($turnStill->inViolation());
    }
}

final class TurnStill
{
    /**
     * @var TurnStillState|StateMetadata
     */
    private $state;

    /**
     * @var int
     */
    private $coins = 0;

    public function __construct()
    {
        $this->state = new TurnStillState('locked');
    }

    public function pay(int $coin): void
    {
        $this->state = $this->state->transit(
            'pay',
            $this,
            new CallContextMethodOnFailure(
                'violation',
                'refund',
                [$coin]
            )
        );

        $this->coins += $coin;
    }

    public function pass(): void
    {
        $this->state = $this->state->transit(
            'pass',
            'turn-still',
            new CallClosureOnFailure(
                function () {
                    return $this->state->transit('alarm', $this)->getCurrent();
                }
            )
        );
    }

    public function reset(): void
    {
        $this->state = $this->state->transit('reset', 'turn-still');
    }

    public function isLocked(): bool
    {
        return $this->state->hasAttribute('is_locked');
    }

    public function inViolation(): bool
    {
        return $this->state->isInState('violation');
    }

    public function coins(): int
    {
        return $this->coins;
    }

    public function refund(int $coins): void
    {
        $this->coins -= $coins;
    }
}

final class PayTransition implements StateTransition
{
    public function getName(): string
    {
        return 'pay';
    }

    /**
     * @param RegistryBuilder $registry
     */
    public function onRegister(RegistryBuilder $registry): void
    {
        $registry->registerStartingState($this->getName(), 'locked', []);
        $registry->registerDestinationState($this->getName(), 'unlocked', []);
    }

    public function getDestinationState(): string
    {
        return 'unlocked';
    }
}

final class TriggerAlarm implements TransitionCallback
{
    public function beforeStateChange($context, StateMachine $machine): void
    {
    }

    public function afterStateChange($context, StateMachine $machine): void
    {
    }

    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine): string
    {
        return $machine->transit('alarm', 'turnstill');
    }
}

final class Refund implements TransitionCallback
{
    /**
     * @var int
     */
    private $coin;

    public function __construct(int $coin)
    {
        $this->coin = $coin;
    }

    public function beforeStateChange($context, StateMachine $machine): void
    {
    }

    public function afterStateChange($context, StateMachine $machine): void
    {
    }

    /**
     * @param InvalidStateTransitionException $exception
     * @param mixed $context
     * @param StateMachine $machine
     * @return string
     */
    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine): string
    {
        return 'violation';
    }
}

final class TurnStillState extends StateMetadata
{
    /**
     * Returns the state workflow configuration.
     *
     * @param StateBuilder $builder
     */
    protected function configure(StateBuilder $builder): void
    {
        $builder->allowTransition('pass', 'unlocked', 'locked');
        $builder->allowCustomTransition(new PayTransition());
        // alarm is called on transition failure
        $builder->allowTransition('alarm', ['locked', 'unlocked'], 'violation');
        $builder->allowTransition('reset', 'violation', 'locked');

        $builder->addAttribute('is_locked', ['locked', 'violation']);
    }
}
