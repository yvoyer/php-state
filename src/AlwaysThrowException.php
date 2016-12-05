<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class AlwaysThrowException implements FailureHandler
{
    /**
     * @var \Exception
     */
    private $exceptionClass;

    /**
     * @param null|string $exceptionClass
     */
    public function __construct($exceptionClass = null)
    {
        if (! $exceptionClass) {
            $exceptionClass = InvalidStateTransitionException::class;
        }

        $this->exceptionClass = $exceptionClass;
    }

    /**
     * Launched when a not allowed transition is detected.
     *
     * @param StateContext $context
     * @param State $from
     * @param State $to
     * @throws InvalidStateTransitionException
     */
    public function handleNotAllowedTransition(StateContext $context, State $from, State $to)
    {
        throw new $this->exceptionClass(
            "The transition from '{$from->toString()}' to '{$to->toString()}' is not allowed."
        );
    }
}
