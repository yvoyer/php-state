<?php

namespace Star\Component\State\Tool\Imaging\Adapter;

use claviska\SimpleImage;
use Star\Component\State\Tool\Imaging\Coordinate;
use Star\Component\State\Tool\Imaging\ImageProcessor;

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
        $this->states[$name] = function(SimpleImage $canvas) use ($name, $point) {
            $box = new SimpleImage();
            $box->fromNew(100, 50);
            $box->text(
                $name,
                [
                    'color' => 'black',
                    'fontFile' => dirname(__DIR__) . '/Resources/fonts/Roboto/Roboto-Medium.ttf',
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

        $canvas->fromNew($square * 200, $square * 100, 'white');

        foreach ($this->states as $data) {
            $data($canvas);
        }

        $canvas->toFile($filename, 'image/png');

        return new \SplFileObject($filename);
    }
}
