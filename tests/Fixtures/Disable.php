<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Fixtures;

/**
 * Class Disable
 *
 * @author  Yannick Voyer (http://github.com/yvoyer)
 *
 * @package Star\Component\State\Fixtures
 */
final class Disable extends DomainContextState
{
    public function enable(DomainContext $context)
    {
        $context->setState(new Enable());
    }

    public function disable(DomainContext $context)
    {
        throw LogicException::createInvalidTransition('disabled', 'disabled');
    }

    public function suspend(DomainContext $context)
    {
        throw LogicException::createInvalidTransition('disabled', 'suspended');
    }

    public function isEnabled()
    {
        return false;
    }

    public function isDisabled()
    {
        return true;
    }

    public function isSuspended()
    {
        return false;
    }
}
