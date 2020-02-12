<?php declare(strict_types=1);
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State;

final class TestContext
{
    /**
     * @var string
     */
    private $current;

    /**
     * @param string $initial
     */
    public function __construct($initial)
    {
        $this->current = $initial;
    }
}
