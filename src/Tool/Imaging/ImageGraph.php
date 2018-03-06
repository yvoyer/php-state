<?php

namespace Star\Component\State\Tool\Imaging;

use Webmozart\Assert\Assert;

final class ImageGraph
{
    /**
     * @var ImagingState[]
     */
    private $states = [];

    /**
     * @param string[] $states
     */
    public function __construct(array $states)
    {
        Assert::allString($states);
        array_map(
            function ($state) {
                $this->states[$state] = new ImagingState($state);
            },
            array_unique($states)
        );
    }

    /**
     * @param string $name
     * @param string $from
     * @param string $to
     */
    public function addTransition($name, $from, $to)
    {
        Assert::string($name);
        Assert::string($from);
        Assert::string($to);
        $from = $this->getState($from);
        $from->addTransition($name, $this->getState($to));
    }

    /**
     * @param string $state
     *
     * @return ImagingState
     */
    private function getState($state)
    {
        if (! array_key_exists($state, $this->states)) {
            throw new \InvalidArgumentException("The state '{$state}' do not exists in the graph.");
        }

        return $this->states[$state];
    }

    /**
     * @return ImagingState[]
     */
    public function getStates()
    {
        return array_values($this->states);
    }

    public function render()
    {
        foreach ($this->states as $name => $state) {
            $state->render();
        }
    }
}
