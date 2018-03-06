<?php

namespace Star\Component\State\Tool\Imaging;

interface ImageProcessor
{
    /**
     * @param string $state
     * @param Coordinate $coordinate
     */
    public function drawState($state, Coordinate $coordinate);

    /**
     * @param string $filename
     *
     * @return \SplFileInfo
     */
    public function createPng($filename);
}
