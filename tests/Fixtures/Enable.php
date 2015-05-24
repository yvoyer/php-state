<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Fixtures;

/**
 * Class Enable
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State\Fixtures
 */
final class Enable extends DomainContextState
{
    public function enable(DomainContext $context)
    {
        throw LogicException::createInvalidTransition('enabled', 'enabled');
    }

    public function disable(DomainContext $context)
    {
        $context->setState(new Disable());
    }

    public function suspend(DomainContext $context)
    {
        $context->setState(new Suspend());
    }

    public function isEnabled()
    {
        return true;
    }

    public function isDisabled()
    {
        return false;
    }

    public function isSuspended()
    {
        return false;
    }
}
