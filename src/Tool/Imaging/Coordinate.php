<?php

namespace Star\Component\State\Tool\Imaging;

use Webmozart\Assert\Assert;

final class Coordinate
{
    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * @param int $x
     * @param int $y
     */
    public function __construct($x, $y)
    {
        Assert::integer($x);
        Assert::integer($y);
        Assert::greaterThan($x, 0, 'Coordinate x expected a value greater than %2$s. Got: %s.');
        Assert::greaterThan($y, 0, 'Coordinate y expected a value greater than %2$s. Got: %s.');
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return int
     */
    public function x()
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function y()
    {
        return $this->y;
    }

    /**
     * @param int $int
     *
     * @return Coordinate
     */
    public function moveRight($int)
    {
        return new Coordinate($this->x + $int, $this->y);
    }

    /**
     * @param int $int
     *
     * @return Coordinate
     */
    public function moveDown($int)
    {
        return new Coordinate($this->x, $this->y + $int);
    }

    /**
     * @param int $spacing
     *
     * @return Coordinate
     */
    public function newRow($spacing)
    {
        return new Coordinate($spacing, $this->y + $spacing);
    }

    /**
     * @param int $spacing
     *
     * @return Coordinate
     */
    public function newColumn($spacing)
    {
        return new Coordinate($this->x + $spacing, $spacing);
    }

    /**
     * @param Coordinate $coordinate
     *
     * @return Size
     */
    public function getSize(Coordinate $coordinate)
    {
        $width = 0;
        if ($this->x > $coordinate->x) {
            $width = $this->x - $coordinate->x + 1;
        } elseif ($this->x < $coordinate->x) {
            $width = $coordinate->x - $this->x + 1;
        }

        $height = 0;
        if ($this->y > $coordinate->y) {
            $height = $this->y - $coordinate->y + 1;
        } elseif ($this->y < $coordinate->y) {
            $height = $coordinate->y - $this->y + 1;
        }

        return new Size($width, $height);
    }

    /**
     * @param int $x
     * @param int $y When no y given, resize diagonal, otherwise also apply to y
     *
     * @return Coordinate
     */
    public function resize($x, $y = null)
    {
        if (is_null($y)) {
            $y = $x;
        }
        Assert::integer($x);
        Assert::integer($y);

        return new Coordinate($this->x + $x, $this->y + $y);
    }

    /**
     * @param Size $size
     *
     * @return Coordinate
     */
    public function addSize(Size $size)
    {
        return new Coordinate($this->x + $size->getWidth(), $this->y + $size->getHeight());
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->x . ',' . $this->y;
    }
}
