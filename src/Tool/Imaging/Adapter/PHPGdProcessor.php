<?php

namespace Star\Component\State\Tool\Imaging\Adapter;

use claviska\SimpleImage;
use Star\Component\State\Tool\Imaging\Coordinate;
use Star\Component\State\Tool\Imaging\ImageProcessor;

final class PHPGdProcessor implements ImageProcessor
{
    /**
     * @var SimpleImage
     */
    private $image;

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
        if (! $this->image) {
            $this->image = new SimpleImage();
            $this->image->fromNew(500, 500, 'white');
        }

        $box = new SimpleImage();
        $box->fromNew(100, 50);
        $box->text($name, ['color' => 'black', 'fontFile' => dirname(__DIR__) . '/Resources/fonts/Roboto/Roboto-Medium.ttf']);
        $box->border('black', 2);

        $this->image->overlay($box, 'center', 1, $point->x(), $point->y());
    }

    /**
     * @param string $filename
     *
     * @return \SplFileInfo
     */
    public function createPng($filename)
    {
        $this->image->toFile($filename, 'image/png');

        return new \SplFileObject($filename);
    }
}
