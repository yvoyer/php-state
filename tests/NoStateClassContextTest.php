<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Fixtures\NoStateClassContext;

/**
 * @author  Yannick Voyer (http://github.com/yvoyer)
 */
final class NoStateClassContextTest extends StateHandlingContextTest
{
    protected function getContext()
    {
        return new NoStateClassContext();
    }
}
