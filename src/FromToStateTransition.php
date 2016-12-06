<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Webmozart\Assert\Assert;

final class FromToStateTransition implements StateTransition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var State
     */
    private $from;

    /**
     * @var State
     */
    private $to;

    /**
     * @param string $name
     * @param State $from
     * @param State $to
     */
    public function __construct($name, State $from, State $to)
    {
        Assert::string($name, "The transition's name must be a string, got '%s'.");
        $this->name = $name;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param State $from
     *
     * @return bool
     */
    public function hasChanged(State $from)
    {
        return $this->from->matchState($from);
    }

    /**
     * @param StateContext $context
     *
     * @return bool
     */
    public function isAllowed(StateContext $context)
    {
        return true; // always allowed
    }

    /**
     * @param StateContext $context
     */
    public function applyStateChange(StateContext $context)
    {
        $context->setState($this->to);
    }
}
