<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Adapter\Symfony;

final class ReflectionPropertyAccessorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ReflectionPropertyAccessor
     */
    private $accessor;

    public function setUp()
    {
        $this->accessor = new ReflectionPropertyAccessor();
    }

    /**
     * @dataProvider providePropertyPath
     */
    public function test_it_should_access_property_on_object($expected, $path)
    {
        $this->assertSame($expected, $this->accessor->getValue(new Subject(), $path));
    }

    public static function providePropertyPath()
    {
        yield ['private', 'private'];
        yield ['protected', 'protected'];
        yield ['public', 'public'];
    }

    /**
     * @expectedException        \Symfony\Component\PropertyAccess\Exception\AccessException
     * @expectedExceptionMessage The property 'not_found' could not be found on subject.
     */
    public function test_it_should_throw_exception_when_property_not_found()
    {
        $object = new Subject();
        $this->assertFalse($this->accessor->isReadable($object, 'not_found'));
        $this->accessor->getValue($object, 'not_found');
    }

    /**
     * @expectedException \Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException
     */
    public function test_it_should_throw_exception_when_subject_not_an_object()
    {
        $object = [];
        $this->assertFalse($this->accessor->isReadable($object, 'not_found'));
        $this->accessor->getValue($object, 'not_found');
    }

    /**
     * @dataProvider providePropertyPath
     */
    public function test_it_should_write_on_property_of_object($expected, $path)
    {
        $subject = new Subject();
        $this->assertTrue($this->accessor->isWritable($subject, $path));
        $this->accessor->setValue($subject, $path, $expected);
        $this->assertSame($expected, $this->accessor->getValue($subject, $path));
    }

    /**
     * @expectedException        \Symfony\Component\PropertyAccess\Exception\AccessException
     * @expectedExceptionMessage The property 'not_found' could not be found on subject.
     */
    public function test_it_should_throw_exception_when_property_path_not_found()
    {
        $subject = new Subject();
        $this->assertFalse($this->accessor->isWritable($subject, 'not_found'));
        $this->accessor->setValue($subject, 'not_found', 'value');
    }

    /**
     * @expectedException \Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException
     */
    public function test_it_should_throw_exception_when_subject_not_an_object_on_set()
    {
        $subject = [];
        $this->assertFalse($this->accessor->isWritable($subject, 'not_found'));
        $this->accessor->setValue($subject, 'not_found', 'value');
    }
}

final class Subject
{
    private $private = 'private';
    protected $protected = 'protected';
    public $public = 'public';
}
