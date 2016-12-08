<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Webmozart\Assert\Assert;

final class OneToOneTransition implements StateTransition
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
     * @param StateContext $context
     *
     * @return bool
     */
    public function changeIsRequired(StateContext $context)
    {
        return ! $this->to->matchState($context->getCurrentState());
    }

    /**
     * @param StateContext $context
     *
     * @return bool
     */
    public function isAllowed(StateContext $context)
    {
        return $this->from->matchState($context->getCurrentState());
    }

    /**
     * @param StateContext $context
     */
    public function applyStateChange(StateContext $context)
    {
        $context->setState($this->to);
    }

    /**
     * @param string $context
     * @param TransitionRegistry $registry
     */
    public function register($context, TransitionRegistry $registry)
    {
        $registry->addState($this->from, $context);
        $registry->addState($this->to, $context);
    }
}
