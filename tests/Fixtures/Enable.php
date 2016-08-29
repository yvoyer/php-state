<?php
/**
 * This file is part of the status project.
 *
 * (c) Yannick Voyer (http://github.com/yvoyer)
 */

namespace Star\Component\State\Fixtures;

/**
 * @author  Yannick Voyer (http://github.com/yvoyer)
 */
final class Enable extends DomainContextState
{
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

    /**
     * The value representation of your state object for reconstruction or storage on your context.
     *
     * @return mixed
     */
    public function stateValue()
    {
        return 'enabled';
    }
}
