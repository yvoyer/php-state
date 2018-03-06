<?php

namespace Star\Component\State\Tool\Imaging;

use Webmozart\Assert\Assert;

final class ImagingState
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var ImagingState[]
     */
    private $transitions = [];

    /**
     * @param string $name
     */
    public function __construct($name)
    {
        Assert::string($name);
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @param ImagingState $state
     */
    public function addTransition($name, ImagingState $state)
    {
        Assert::string($name);
        if (array_key_exists($name, $this->transitions)) {
            throw new \InvalidArgumentException(
                "The transition '{$name}' is already defined for the starting state '{$this->name}'."
            );
        }

        $this->transitions[$name] = $state;
    }

    public function render(ImageProcessor $processor)
    {
        $processor->renderState($this->name);
        foreach ($this->transitions as $transition => $toState) {
            $processor->renderTransition($transition, $this->name, $toState->name);
        }
    }
}
