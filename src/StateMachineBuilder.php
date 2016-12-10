<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Adapter\Symfony\ReflectionPropertyAccessor;
use Symfony\Component\Workflow\Definition;
use Symfony\Component\Workflow\MarkingStore\MultipleStateMarkingStore;
use Symfony\Component\Workflow\MarkingStore\SingleStateMarkingStore;
use Symfony\Component\Workflow\Workflow;

final class StateMachineBuilder
{
    /**
     * @var Workflow
     */
    private $workflow;

    /**
     * @var string[]
     */
    private $states = [];

    /**
     * @param Workflow $workflow
     */
    private function __construct($name)
    {

    }

    /**
     * @param string $state
     *
     * @return StateMachineBuilder
     */
    public function registerState($state)
    {
        $this->states[$state] = 1;

        return $this;
    }

    public function getMachine($initial)
    {
        $definition = new Definition(array_keys($this->states), [], $initial);
        return new Workflow(
            $definition,
            new MultipleStateMarkingStore('state', new ReflectionPropertyAccessor())
        );
    }

    public static function create($name)
    {
        return new self($name);
    }
}
