<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Event;

use Symfony\Component\EventDispatcher\Event;
use Webmozart\Assert\Assert;

/**
 * @experimental
 */
final class TransitionWasSuccessful extends Event
{
    /**
     * @var string
     */
    private $transition;

    /**
     * @param string $transition
     */
    public function __construct($transition)
    {
        Assert::string($transition);
        $this->transition = $transition;
    }

    /**
     * @return string
     */
    public function transition()
    {
        return $this->transition;
    }
}
