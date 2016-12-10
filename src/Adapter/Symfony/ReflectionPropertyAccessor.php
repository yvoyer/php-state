<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Adapter\Symfony;

use Symfony\Component\PropertyAccess\Exception;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\PropertyAccess\PropertyPathInterface;

final class ReflectionPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * Sets the value at the end of the property path of the object graph.
     *
     * Example:
     *
     *     use Symfony\Component\PropertyAccess\PropertyAccess;
     *
     *     $propertyAccessor = PropertyAccess::createPropertyAccessor();
     *
     *     echo $propertyAccessor->setValue($object, 'child.name', 'Fabien');
     *     // equals echo $object->getChild()->setName('Fabien');
     *
     * This method first tries to find a public setter for each property in the
     * path. The name of the setter must be the camel-cased property name
     * prefixed with "set".
     *
     * If the setter does not exist, this method tries to find a public
     * property. The value of the property is then changed.
     *
     * If neither is found, an exception is thrown.
     *
     * @param object|array $objectOrArray The object or array to modify
     * @param string|PropertyPathInterface $propertyPath The property path to modify
     * @param mixed $value The value to set at the end of the property path
     *
     * @throws Exception\InvalidArgumentException If the property path is invalid
     * @throws Exception\AccessException          If a property/index does not exist or is not public
     * @throws Exception\UnexpectedTypeException  If a value within the path is neither object nor array
     */
    public function setValue(&$objectOrArray, $propertyPath, $value)
    {
        if (! is_object($objectOrArray)) {
            throw new Exception\UnexpectedTypeException($objectOrArray, new PropertyPath($propertyPath), 0);
        }

        if (! $this->isWritable($objectOrArray, $propertyPath)) {
            throw new Exception\AccessException("The property '{$propertyPath}' could not be found on subject.");
        }

        $ref = new \ReflectionClass($objectOrArray);
        $property = $ref->getProperty($propertyPath);
        $property->setAccessible(true);
        $property->setValue($objectOrArray, $value);
    }

    /**
     * Returns the value at the end of the property path of the object graph.
     *
     * Example:
     *
     *     use Symfony\Component\PropertyAccess\PropertyAccess;
     *
     *     $propertyAccessor = PropertyAccess::createPropertyAccessor();
     *
     *     echo $propertyAccessor->getValue($object, 'child.name);
     *     // equals echo $object->getChild()->getName();
     *
     * This method first tries to find a public getter for each property in the
     * path. The name of the getter must be the camel-cased property name
     * prefixed with "get", "is", or "has".
     *
     * If the getter does not exist, this method tries to find a public
     * property. The value of the property is then returned.
     *
     * If none of them are found, an exception is thrown.
     *
     * @param object|array $objectOrArray The object or array to traverse
     * @param string|PropertyPathInterface $propertyPath The property path to read
     *
     * @return mixed The value at the end of the property path
     *
     * @throws Exception\InvalidArgumentException If the property path is invalid
     * @throws Exception\AccessException          If a property/index does not exist or is not public
     * @throws Exception\UnexpectedTypeException  If a value within the path is neither object
     *                                            nor array
     */
    public function getValue($objectOrArray, $propertyPath)
    {
        if (! is_object($objectOrArray)) {
            throw new Exception\UnexpectedTypeException($objectOrArray, new PropertyPath($propertyPath), 0);
        }

        if (! $this->isReadable($objectOrArray, $propertyPath)) {
            throw new Exception\AccessException("The property '{$propertyPath}' could not be found on subject.");
        }

        $ref = new \ReflectionClass($objectOrArray);
        $property = $ref->getProperty($propertyPath);
        $property->setAccessible(true);

        return $property->getValue($objectOrArray);
    }

    /**
     * Returns whether a value can be written at a given property path.
     *
     * Whenever this method returns true, {@link setValue()} is guaranteed not
     * to throw an exception when called with the same arguments.
     *
     * @param object|array $objectOrArray The object or array to check
     * @param string|PropertyPathInterface $propertyPath The property path to check
     *
     * @return bool Whether the value can be set
     *
     * @throws Exception\InvalidArgumentException If the property path is invalid
     */
    public function isWritable($objectOrArray, $propertyPath)
    {
        return $this->isReadable($objectOrArray, $propertyPath);
    }

    /**
     * Returns whether a property path can be read from an object graph.
     *
     * Whenever this method returns true, {@link getValue()} is guaranteed not
     * to throw an exception when called with the same arguments.
     *
     * @param object|array $objectOrArray The object or array to check
     * @param string|PropertyPathInterface $propertyPath The property path to check
     *
     * @return bool Whether the property path can be read
     *
     * @throws Exception\InvalidArgumentException If the property path is invalid
     */
    public function isReadable($objectOrArray, $propertyPath)
    {
        if (! is_object($objectOrArray)) {
            return false;
        }

        $ref = new \ReflectionClass($objectOrArray);

        return $ref->hasProperty($propertyPath);
    }
}
