<?php

namespace Star\Component\State\Event;

use Symfony\Component\EventDispatcher\Event;

final class TransitionWasFailed extends Event
{
    /**
     * @var string
     */
    private $transition;

    /**
     * @var \Exception
     */
    private $exception;

    /**
     * @param string $transition
     * @param \Exception $exception
     */
    public function __construct($transition, \Exception $exception)
    {
        $this->transition = $transition;
        $this->exception = $exception;
    }

    /**
     * @return string
     */
    public function transition()
    {
        return $this->transition;
    }

    /**
     * @return \Exception
     */
    public function exception()
    {
        return $this->exception;
    }
}
