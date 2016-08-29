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
final class Disable extends DomainContextState
{
    public function enable(DomainContext $context)
    {
        $context->setState(new Enable());
    }

    public function isDisabled()
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
        return 'disabled';
    }
}
