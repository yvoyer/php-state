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
        $this->exceptionClass = $exceptionClass;
    }

    /**
     * Launched when a not allowed transition is detected.
     *
     * @param StateContext $context
     * @param StateTransition $transition
     */
    public function handleTransitionNotAllowed(StateContext $context, StateTransition $transition)
    {
        $exceptionClass = $this->exceptionClass;
        if (! $this->exceptionClass) {
            $exceptionClass = InvalidStateTransitionException::class;
        }

        throw new $exceptionClass(
            "The transition '{$transition->name()}' is not allowed on context '{$context->contextAlias()}'."
        );
    }

    /**
     * Launched when a no transition are found for the context and state.
     *
     * @param string $name
     * @param string $context
     */
    public function handleStateNotFound($name, $context)
    {
        $exceptionClass = $this->exceptionClass;
        if (! $this->exceptionClass) {
            $exceptionClass = NotFoundException::class;
        }

        throw new $exceptionClass(sprintf("The state '%s' could not be found for context '%s'.", $name, $context));
    }

    /**
     * @param string $name The transition name
     * @param string $context The context alias
     * @throws NotFoundException
     */
    public function handleTransitionNotFound($name, $context)
    {
        $exceptionClass = $this->exceptionClass;
        if (! $this->exceptionClass) {
            $exceptionClass = NotFoundException::class;
        }

        throw new $exceptionClass(sprintf("The transition '%s' could not be found for context '%s'.", $name, $context));
    }
}
