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
     * @param StateTransition $transition
     */
    public function handleNotAllowedTransition(StateContext $context, StateTransition $transition)
    {
        throw new $this->exceptionClass(
            "The transition '{$transition->name()}' is not allowed on context '{$context->contextAlias()}'."
        );
    }
}
