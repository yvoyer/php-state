<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Attibute;

use Webmozart\Assert\Assert;

final class StringAttribute implements StateAttribute
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __construct($key, $value = null)
    {
        Assert::string($key, 'Attribute key must be a string. Got: %s');

        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->key;
    }
}
