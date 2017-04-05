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
		    $exceptionClass = NotFoundException::class;
	    }

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
        throw $this->generateException(
	        sprintf(
		        "The transition '%s' is not allowed when context '%s' is in state XXX(pass machine to get curtrent.",
		        $transition->name(),
		        get_class($context)
	        )
        );
    }

    /**
     * Launched when a no transition are found for the context and state.
     *
     * @param string $name
     */
    public function handleStateNotFound($name)
    {
        throw $this->generateException(sprintf("The state '%s' could not be found.", $name));
    }

    /**
     * @param string $name The transition name
     * @throws NotFoundException
     */
    public function handleTransitionNotFound($name)
    {
        throw $this->generateException(sprintf("The transition '%s' could not be found.", $name));
    }

	/**
	 * Launched when a state is already registered
	 *
	 * @param string $name
	 */
	public function onStateAlreadyRegistered($name) {
		throw $this->generateException(sprintf("The state '%s' is already registered.", $name));
	}

	/**
	 * Launched when a transition is already registered.
	 *
	 * @param string $name
	 */
	public function onTransitionAlreadyRegistered($name) {
		throw $this->generateException(sprintf("The transition '%s' is already registered.", $name));
	}

	private function generateException($message)
	{
		return new $this->exceptionClass($message);
	}
}
