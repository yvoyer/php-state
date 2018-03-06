<?php

namespace Star\Component\State\Tool\Imaging;

use PHPUnit\Framework\TestCase;

final class ShapeSizeTest extends TestCase
{
    public function test_it_should_have_width()
    {
        $size = new Size(3, 2);
        $this->assertSame(3, $size->getWidth());
    }

    public function test_it_should_have_height()
    {
        $size = new Size(3, 2);
        $this->assertSame(2, $size->getHeight());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Width expected a value greater or equal than 0. Got: -1.
     */
    public function test_width_should_be_int_greater_than_zero()
    {
        new Size(-1, 1);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Height expected a value greater or equal than 0. Got: -1.
     */
    public function test_height_should_be_int_greater_than_zero()
    {
        new Size(1, -1);
    }

    public function test_it_should_add_width()
    {
        $size = new Size(5, 5);
        $this->assertEquals(
            new Size(9, 5),
            $size->addWidth(4)
        );
    }

    public function test_it_should_add_height()
    {
        $size = new Size(5, 5);
        $this->assertEquals(
            new Size(5, 9),
            $size->addHeight(4)
        );
    }

    public function test_it_should_add_width_using_object()
    {
        $size = new Size(5, 5);
        $this->assertEquals(
            new Size(9, 5),
            $size->expand(new Size(4, 0))
        );
    }

    public function test_it_should_add_height_using_object()
    {
        $size = new Size(5, 5);
        $this->assertEquals(
            new Size(5, 9),
            $size->expand(new Size(0, 4))
        );
    }
}
