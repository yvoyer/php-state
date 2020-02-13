<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Event;

use Symfony\Component\EventDispatcher\Event;

final class TransitionWasSuccessful extends Event implements StateEvent
{
    /**
     * @var string
     */
    private $transition;

    public function __construct(string $transition)
    {
        $this->transition = $transition;
    }

    public function transition(): string
    {
        return $this->transition;
    }
}
