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
     * @param string $transition
     * @param string $from
     * @param string $to
     */
    public function drawTransition($transition, $from, $to);

    /**
     * @param string $filename
     *
     * @return \SplFileInfo
     */
    public function createPng($filename);
}
