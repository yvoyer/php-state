<?php

namespace Star\Component\State\Tool\Imaging;

use Webmozart\Assert\Assert;

final class Size
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    /**
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        Assert::integer($width);
        Assert::integer($height);
        Assert::greaterThanEq($width, 0, 'Width expected a value greater or equal than %2$s. Got: %s.');
        Assert::greaterThanEq($height, 0, 'Height expected a value greater or equal than %2$s. Got: %s.');
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * @param int $int
     *
     * @return Size
     */
    public function addWidth($int)
    {
        return new Size($this->width + $int, $this->height);
    }

    /**
     * @param int $int
     *
     * @return Size
     */
    public function addHeight($int)
    {
        return new Size($this->width, $this->height + $int);
    }

    /**
     * @param Size $size
     *
     * @return Size
     */
    public function expand(Size $size)
    {
        return $this->addWidth($size->width)->addHeight($size->height);
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
}
