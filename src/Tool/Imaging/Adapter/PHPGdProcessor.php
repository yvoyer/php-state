<?php

namespace Star\Component\State\Tool\Imaging\Adapter;

use claviska\SimpleImage;
use Star\Component\State\Tool\Imaging\Coordinate;
use Star\Component\State\Tool\Imaging\ImageProcessor;
use Star\Component\State\Tool\Imaging\Size;
use Webmozart\Assert\Assert;

final class PHPGdProcessor implements ImageProcessor
{
    /**
     * @var Coordinate
     */
    private $lastCoordinate;

    /**
     * @var \Closure[]
     */
    private $states = [];

    /**
     * @var \Closure[]
     */
    private $transitions = [];

    /**
     * @var Coordinate[]
     */
    private $points = [];

    public function __construct()
    {
        if (! extension_loaded('gd')) {
            throw new \RuntimeException('PHP gd extension is not loaded.');
        }
    }

    /**
     * @param string $name
     * @param Coordinate $point The point of the left side corner
     */
    public function drawState($name, Coordinate $point)
    {
        $this->points[$name] = $point->moveRight(50)->moveDown(25);

        $this->states[$name] = function(SimpleImage $canvas) use ($name, $point) {
            $box = new SimpleImage();
            $box->fromNew(100, 50);
            $box->text(
                $name,
                [
                    'color' => 'black',
                    'fontFile' => $this->getFont(),
                ]
            );
            $box->border('black', 2);

            $canvas->overlay(
                $box,
                'top left',
                1,
                $point->x() * 1.5,
                $point->y()
            );
        };

        $this->lastCoordinate = $point;
    }

    /**
     * @param string $transition
     * @param string $from
     * @param string $to
     */
    public function drawTransition($transition, $from, $to)
    {
        Assert::string($transition);
        Assert::string($from);
        Assert::string($to);

        $fromPoint = $this->points[$from];
        $toPoint = $this->points[$to];

        var_dump($fromPoint, $toPoint);

        $this->transitions[$transition] = function(SimpleImage $canvas, Size $size) use (
            $transition,
            $fromPoint,
            $toPoint
        ) {
            $line = new SimpleImage();
            $line->fromNew($size->getWidth(), $size->getHeight());
            $line->text(
                $transition,
                [
                    'color' => 'red',
                    'anchor' => 'bottom',
                    'fontFile' => $this->getFont(),
                ]
            );
            $line->line(
                $fromPoint->x(),
                $fromPoint->y(),
                $toPoint->x(),
                $toPoint->y(),
                'red',
                2
            );

            $canvas->overlay($line);
        };
    }

    /**
     * @param string $filename
     *
     * @return \SplFileInfo
     */
    public function createPng($filename)
    {
        $canvas = new SimpleImage();
        $square = sqrt(count($this->states));
        if ($square !== (int) $square) {
            $square = (int) $square + 1;
        }

        $canvasSize = new Size($square * 200, $square * 100);
        $canvas->fromNew($canvasSize->getWidth(), $canvasSize->getHeight(), 'white');

        foreach ($this->states as $data) {
            $data($canvas);
        }

        foreach ($this->transitions as $transition => $callback) {
            $callback($canvas, $canvasSize);
        }

        $canvas->toFile($filename, 'image/png');

        return new \SplFileObject($filename);
    }

    /**
     * @return string
     */
    private function getFont()
    {
        return dirname(__DIR__) . '/Resources/fonts/Roboto/Roboto-Medium.ttf';
    }
}
