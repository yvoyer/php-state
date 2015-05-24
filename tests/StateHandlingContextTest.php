<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Fixtures\DomainContext;

/**
 * Class StateHandlingContextTest
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State
 */
abstract class StateHandlingContextTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DomainContext
     */
    private $context;

    protected abstract function getContext();

    public function setUp()
    {
        $this->context = $this->getContext();
    }

    public function test_it_should_be_disabled_by_default()
    {
        $this->assertTrue($this->context->isDisabled());
    }

    public function test_it_can_be_enabled_when_disabled()
    {
        $this->assertFalse($this->context->isEnabled());
        $this->context->enable();
        $this->assertTrue($this->context->isEnabled());
    }

    public function test_it_can_be_suspended_when_enabled()
    {
        $this->context->enable();
        $this->assertFalse($this->context->isSuspended());
        $this->context->suspend();
        $this->assertTrue($this->context->isSuspended());
    }

    public function test_it_can_be_disabled_when_enabled()
    {
        $this->context->enable();
        $this->assertFalse($this->context->isDisabled());
        $this->context->disable();
        $this->assertTrue($this->context->isDisabled());
    }

    public function test_it_can_be_enabled_when_suspended()
    {
        $this->context->enable();
        $this->context->suspend();
        $this->assertFalse($this->context->isEnabled());
        $this->context->enable();
        $this->assertTrue($this->context->isEnabled());
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage The state cannot be suspended when disabled.
     */
    public function test_it_should_not_be_suspended_when_disabled()
    {
        $this->assertFalse($this->context->isSuspended());
        $this->context->suspend();
        $this->assertTrue($this->context->isSuspended());
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage The state cannot be disabled when disabled.
     */
    public function test_it_should_not_be_disabled_when_disabled()
    {
        $this->assertTrue($this->context->isDisabled());
        $this->context->disable();
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage The state cannot be disabled when suspended.
     */
    public function test_it_should_not_be_disabled_when_suspended()
    {
        $this->context->enable();
        $this->context->suspend();
        $this->assertFalse($this->context->isDisabled());
        $this->context->disable();
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage The state cannot be enabled when enabled.
     */
    public function test_it_should_not_be_enabled_when_enabled()
    {
        $this->context->enable();
        $this->assertTrue($this->context->isEnabled());
        $this->context->enable();
    }

    /**
     * @expectedException        \LogicException
     * @expectedExceptionMessage The state cannot be suspended when suspended.
     */
    public function test_it_should_not_be_suspended_when_suspended()
    {
        $this->context->enable();
        $this->context->suspend();
        $this->context->suspend();
    }
}
