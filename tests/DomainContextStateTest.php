<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State;

use Star\Component\State\Fixtures\NoStateClassContext;

/**
 * Class DomainContextStateTest
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State
 */
final class DomainContextStateTest extends StateHandlingContextTest
{
    protected function getContext()
    {
        return new NoStateClassContext();
    }
}
