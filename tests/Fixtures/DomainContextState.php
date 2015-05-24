<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Fixtures;

use Star\Component\State\State;

/**
 * Class DomainContextState
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State\Fixtures
 */
abstract class DomainContextState implements State
{
    public abstract function enable(DomainContext $context);

    public abstract function disable(DomainContext $context);

    public abstract function suspend(DomainContext $context);

    public abstract function isEnabled();

    public abstract function isDisabled();

    public abstract function isSuspended();
}
