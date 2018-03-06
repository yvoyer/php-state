<?php

namespace Star\Component\State\Tool\Imaging;

use PHPUnit\Framework\TestCase;

final class CoordinateTest extends TestCase
{
    public function test_it_should_have_x_y_positions()
    {
        $coordinate = new Coordinate(3, 2);
        $this->assertSame('3,2', $coordinate->toString());
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Coordinate x expected a value greater than 0. Got: -1.
     */
    public function test_x_coordinate_should_be_int_greater_than_zero()
    {
        new Coordinate(-1, 1);
    }

    /**
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Coordinate y expected a value greater than 0. Got: -1.
     */
    public function test_y_coordinate_should_be_int_greater_than_zero()
    {
        new Coordinate(1, -1);
    }

    /**
     * 3 |  1
     * 2 |
     * 1 |        2
     * 0 +--+--+--+
     *   0  1  2  3
     */
    public function test_it_should_return_the_size_when_left_is_before_right()
    {
        $point1 = new Coordinate(1, 3);
        $this->assertEquals(
            new Size(3, 3),
            $point1->getSize(new Coordinate(3, 1))
        );
    }

    /**
     * 3 |  2
     * 2 |
     * 1 |        1
     * 0 +--+--+--+
     *   0  1  2  3
     */
    public function test_it_should_return_the_size_when_right_is_before_left()
    {
        $point1 = new Coordinate(3, 1);
        $this->assertEquals(
            new Size(3, 3),
            $point1->getSize(new Coordinate(1, 3))
        );
    }

    /**
     * 3 |
     * 2 |
     * 1 |  1     2
     * 0 +--+--+--+
     *   0  1  2  3
     */
    public function test_it_should_return_the_size_when_both_on_same_y()
    {
        $point2 = new Coordinate(1, 1);
        $this->assertEquals(
            new Size(3, 0),
            $point2->getSize(new Coordinate(3, 1))
        );
    }

    /**
     * 3 |        1
     * 2 |
     * 1 |        2
     * 0 +--+--+--+
     *   0  1  2  3
     */
    public function test_it_should_return_the_size_when_both_on_same_x()
    {
        $point2 = new Coordinate(3, 1);
        $this->assertEquals(
            new Size(0, 3),
            $point2->getSize(new Coordinate(3, 3))
        );
    }

    public function test_it_should_resize_diagonal()
    {
        $point = new Coordinate(5, 5);
        $this->assertEquals(
            new Coordinate(10, 10),
            $point->resize(5)
        );
    }

    public function test_it_should_resize_on_x_axis()
    {
        $point = new Coordinate(5, 5);
        $this->assertEquals(
            new Coordinate(10, 5),
            $point->resize(5, 0)
        );
    }

    public function test_it_should_resize_on_y_axis()
    {
        $point = new Coordinate(5, 5);
        $this->assertEquals(
            new Coordinate(10, 6),
            $point->resize(5, 1)
        );
    }

    public function test_it_should_add_size_to_coordinate()
    {
        $point = new Coordinate(3, 2);
        $this->assertEquals(
            new Coordinate(5, 5),
            $point->addSize(new Size(2, 3))
        );
    }

    public function test_it_should_move_coordinate_right()
    {
        $point = new Coordinate(5, 5);
        $this->assertEquals(
            new Coordinate(10, 5),
            $point->moveRight(5)
        );
    }

    public function test_it_should_move_coordinate_down()
    {
        $point = new Coordinate(5, 5);
        $this->assertEquals(
            new Coordinate(5, 10),
            $point->moveDown(5)
        );
    }

    public function test_it_should_go_down_to_start_of_row()
    {
        $point = new Coordinate(10, 5);
        $this->assertEquals(
            new Coordinate(5, 10),
            $point->newRow(5)
        );
    }

    public function test_it_should_go_up_to_start_of_column()
    {
        $point = new Coordinate(20, 25);
        $this->assertEquals(
            new Coordinate(25, 5),
            $point->newColumn(5)
        );
    }
}
