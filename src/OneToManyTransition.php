<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Webmozart\Assert\Assert;

final class OneToManyTransition implements StateTransition
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var State
     */
    private $from;

    /**
     * @var State[]
     */
    private $tos = [];

    public function __construct($name, State $from, array $tos)
    {
        Assert::string($name, "Transition name must be a string, '%s' given.");
        Assert::allIsInstanceOf($tos, State::class, "Transition's destinations must be instance of '%s'.");

        $this->name = $name;
        $this->from = $from;
        $this->tos = $tos;
    }

    /**
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * @param StateContext $context
     *
     * @return bool
     */
    public function changeIsRequired(StateContext $context)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

    /**
     * @param StateContext $context
     *
     * @return bool
     */
    public function isAllowed(StateContext $context)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

	/**
	 * @param StateContext $context
	 */
	public function beforeStateChange(StateContext $context) {
		throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
	}

    /**
     * @param StateContext $context
     */
    public function onStateChange(StateContext $context)
    {
        throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
    }

	/**
	 * @param StateContext $context
	 */
	public function afterStateChange(StateContext $context) {
		throw new \RuntimeException('Method ' . __METHOD__ . ' not implemented yet.');
	}

    /**
     * @param string $context
     * @param TransitionRegistry $registry
     */
    public function register($context, TransitionRegistry $registry)
    {
        $registry->addState($this->from, $context);
        foreach ($this->tos as $state) {
            $registry->addState($state, $context);
        }
    }
}
