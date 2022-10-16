<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class TransitionWasSuccessful extends Event implements StateEvent
{
    private string $transition;

    public function __construct(string $transition)
    {
        $this->transition = $transition;
    }

    public function transition(): string
    {
        return $this->transition;
    }
}
