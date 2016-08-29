<?php
/**
 * This file is part of the state-pattern project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

/**
 * Implementation used to map custom states to object state
 */
final class MappingState implements State
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * The value representation of your state object for reconstruction or storage on your context.
     *
     * @return mixed
     */
    public function stateValue()
    {
        return $this->value;
    }
}
