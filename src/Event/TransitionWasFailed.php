<?php declare(strict_types=1);

namespace Star\Component\State\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class TransitionWasFailed extends Event implements StateEvent
{
    /**
     * @var string
     */
    private $transition;

    /**
     * @var \Throwable
     */
    private $exception;

    public function __construct(string $transition, \Throwable $exception)
    {
        $this->transition = $transition;
        $this->exception = $exception;
    }

    public function transition(): string
    {
        return $this->transition;
    }

    public function exception(): \Throwable
    {
        return $this->exception;
    }
}
