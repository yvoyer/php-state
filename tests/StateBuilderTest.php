<?php
/**
 * This file is part of the state-pattern project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class StateBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_should_transition_between_basic_constant_state()
    {
        $machine = StateBuilder::create()
            ->allow(BasicContextStub::STATE_DRAFT, BasicContextStub::STATE_ACTIVE, BasicContextStub::class)
            ->allow(BasicContextStub::STATE_ACTIVE, BasicContextStub::STATE_ARCHIVED, BasicContextStub::class)
            ->getStateMachine();

        $context = new BasicContextStub();
        $this->assertTrue($context->isDraft(), 'Initial context should be draft');
        $this->assertFalse($context->isActive());
        $this->assertFalse($context->isArchived());

        $machine->transitTo(BasicContextStub::STATE_ACTIVE, $context);

        $this->assertFalse($context->isDraft());
        $this->assertTrue($context->isActive(), 'Should allow draft to active');
        $this->assertFalse($context->isArchived());

        $machine->transitTo(BasicContextStub::STATE_ARCHIVED, $context);

        $this->assertFalse($context->isDraft());
        $this->assertFalse($context->isActive());
        $this->assertTrue($context->isArchived(), 'Should allow active to archived');
    }
}

final class BasicContextStub implements StateContext
{
    const STATE_DRAFT = 1;
    const STATE_ACTIVE = 2;
    const STATE_ARCHIVED = 3;

    private $state = self::STATE_DRAFT;

    public function isDraft()
    {
        return $this->state === self::STATE_DRAFT;
    }

    public function isActive()
    {
        return $this->state === self::STATE_ACTIVE;
    }

    public function isArchived()
    {
        return $this->state === self::STATE_ARCHIVED;
    }

    /**
     * @param State $state
     */
    public function setState(State $state)
    {
        $this->state = $state->stateValue();
    }
}
