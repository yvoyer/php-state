<?php

namespace Star\Component\State\Example;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\Callbacks\CallContextMethodOnFailure;
use Star\Component\State\Callbacks\ClosureCallback;
use Star\Component\State\Callbacks\TransitionCallback;
use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateMachine;
use Star\Component\State\StateMetadata;
use Star\Component\State\StateRegistry;
use Star\Component\State\StateTransition;
use Star\Component\State\StateVisitor;
use Star\Component\State\TransitionVisitor;

final class CallbackStateTest extends TestCase
{
    public function test_workflow()
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

class TurnStill
{
    /**
     * @var TurnStillState
     */
    private $state;

    private $coins = 0;

    public function __construct()
    {
        $this->state = new TurnStillState('locked');
    }

    /**
     * @param int $coin
     */
    public function pay($coin)
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

    public function pass()
    {
        $this->state = $this->state->transit(
            'pass',
            'turn-still',
            new ClosureCallback(
                function () {
                    return $this->state->transit('alarm', $this)->getCurrent();
                }
            )
        );
    }

    public function reset()
    {
        $this->state = $this->state->transit('reset', 'turn-still');
    }

    public function isLocked()
    {
        return $this->state->hasAttribute('is_locked');
    }

    public function inViolation()
    {
        return $this->state->isInState('violation');
    }

    public function coins()
    {
        return $this->coins;
    }

    /**
     * @param int $coins
     */
    public function refund($coins)
    {
        $this->coins -= $coins;
    }
}

class UnlockTransition implements StateTransition
{
    /**
     * @param string $from
     *
     * @return bool
     */
    public function isAllowed($from)
    {
        return 'locked' === $from;
    }

    /**
     * @param RegistryBuilder $registry
     */
    public function onRegister(RegistryBuilder $registry)
    {
        $registry->registerState('locked', []);
        $registry->registerState('unlocked', []);
    }

    public function getDestinationState()
    {
        return 'unlocked';
    }

    /**
     * @param TransitionVisitor $visitor
     */
    public function acceptTransitionVisitor(TransitionVisitor $visitor)
    {
        $visitor->visitFromState('locked');
        $visitor->visitToState('unlocked');
    }

    /**
     * @param StateVisitor $visitor
     * @param StateRegistry $registry
     */
    public function acceptStateVisitor(StateVisitor $visitor, StateRegistry $registry)
    {
        $registry->getState('locked')->acceptStateVisitor($visitor);
        $registry->getState('unlocked')->acceptStateVisitor($visitor);
    }
}

class TriggerAlarm implements TransitionCallback
{
    public function beforeStateChange($context, StateMachine $machine)
    {
    }

    public function afterStateChange($context, StateMachine $machine)
    {
    }

    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine)
    {
        return $machine->transit('alarm', 'turnstill');
    }
}

class Refund implements TransitionCallback
{
    /**
     * @var int
     */
    private $coin;

    /**
     * @param int $coin
     */
    public function __construct($coin)
    {
        $this->coin = $coin;
    }

    public function beforeStateChange($context, StateMachine $machine)
    {
    }

    public function afterStateChange($context, StateMachine $machine)
    {
    }

    public function onFailure(InvalidStateTransitionException $exception, $context, StateMachine $machine)
    {
        return 'violation';
    }
}

class TurnStillState extends StateMetadata
{
    /**
     * Returns the state workflow configuration.
     *
     * @param StateBuilder $builder
     */
    protected function configure(StateBuilder $builder)
    {
        $builder->allowTransition('pass', 'unlocked', 'locked');
        $builder->allowCustomTransition('pay', new UnlockTransition());
        // alarm is called on transition failure
        $builder->allowTransition('alarm', ['locked', 'unlocked'], 'violation');
        $builder->allowTransition('reset', 'violation', 'locked');

        $builder->addAttribute('is_locked', ['locked', 'violation']);
    }
}
