<?php declare(strict_types=1);

namespace Star\Component\State\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class TransitionWasRequested extends Event implements StateEvent
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
