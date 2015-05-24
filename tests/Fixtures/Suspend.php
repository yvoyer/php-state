<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Fixtures;

/**
 * Class Suspend
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State\Fixtures
 */
final class Suspend extends DomainContextState
{
    public function enable(DomainContext $context)
    {
        $context->setState(new Enable());
    }

    public function disable(DomainContext $context)
    {
        throw LogicException::createInvalidTransition('suspended', 'disabled');
    }

    public function suspend(DomainContext $context)
    {
        throw LogicException::createInvalidTransition('suspended', 'suspended');
    }

    public function isEnabled()
    {
        return false;
    }

    public function isDisabled()
    {
        return false;
    }

    public function isSuspended()
    {
        return true;
    }
}
