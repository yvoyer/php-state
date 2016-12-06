<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Builder;

use Star\Component\State\Attribute\StateAttribute;

final class AttributeBuilderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AttributeBuilder
     */
    private $builder;

    public function setUp()
    {
        $this->builder = new AttributeBuilder();
    }

    public function test_it_can_create_an_attribute_with_a_value()
    {
        $attribute = $this->builder->attribute('name', 'value');
        $this->assertInstanceOf(StateAttribute::class, $attribute);
        $this->assertSame('name', $attribute->name());
        $this->assertTrue($attribute->matchValue('value'));
        $this->assertFalse($attribute->matchValue(true));
    }

    public function test_it_can_create_an_attribute_without_value()
    {
        $attribute = $this->builder->attribute('name');
        $this->assertInstanceOf(StateAttribute::class, $attribute);
        $this->assertSame('name', $attribute->name());
        $this->assertTrue($attribute->matchValue(null));
        $this->assertFalse($attribute->matchValue(true));
    }
}
