<?php

namespace Star\Component\State\Tool\Imaging;

use Star\Component\State\StateMachine;
use Star\Component\State\StateVisitor;
use Star\Component\State\Visitor\TransitionDumper;

final class ImageGenerator implements StateVisitor
{
    /**
     * @var ImageProcessor
     */
    private $processor;

    /**
     * @var Coordinate
     */
    private $nextPosition;

    /**
     * @var int
     */
    private $spacing = 100;

    /**
     * @var string
     */
    private $states = [];

    /**
     * @var int
     */
    private $modulo = 2;

    public function __construct(ImageProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @param string $name File path name
     * @param StateMachine $machine
     *
     * @return \SplFileInfo
     */
    public function generate($name, StateMachine $machine)
    {
        $this->nextPosition = new Coordinate($this->spacing, $this->spacing);
        $machine->acceptStateVisitor($this); // print state boxes
        $machine->acceptTransitionVisitor($dumper = new TransitionDumper()); // print transitions links
        $this->generateLinks($dumper->getStructure());

        return $this->processor->createPng($name);
    }

    /**
     * @param string $name
     * @param string[] $attributes
     */
    public function visitState($name, array $attributes)
    {
        $this->processor->drawState($name, $this->nextPosition);
        $this->states[] = $name;
        $count = count($this->states);

        if ($count < 4) {
            if ($count === $this->modulo) {
                $this->nextPosition = $this->nextPosition->newRow($this->spacing);
            } else {
                $this->nextPosition = $this->nextPosition->moveRight($this->spacing);
            }
        } else {
            if ($count / $this->modulo == $this->modulo - 1) {
                $this->nextPosition = $this->nextPosition->newRow($this->spacing);
            } elseif ($count == pow($this->modulo, 2)) {
                $this->nextPosition = $this->nextPosition->newColumn($this->spacing);
                $this->modulo ++;
            } else if ($count < pow($this->modulo, 2) - $this->modulo) {
                $this->nextPosition = $this->nextPosition->moveDown($this->spacing);
            } else if ($count === pow($this->modulo, 2) - $this->modulo) {
                $this->nextPosition = $this->nextPosition->newRow($this->spacing);
            } else {
                $this->nextPosition = $this->nextPosition->moveRight($this->spacing);
            }
        }
    }

    /**
     * @param array $structure
     */
    private function generateLinks(array $structure)
    {
        foreach ($structure as $transition => $states) {
            foreach ($states['from'] as $from) {
                $this->processor->drawTransition($transition, $from, $states['to']);
            }
        }
    }
}
