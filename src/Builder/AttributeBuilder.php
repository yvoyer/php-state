<?php

namespace Star\Component\State\Builder;

interface AttributeBuilder
{
    /**
     * @param string $attribute
     * @param string|string[] $states
     */
    public function addAttribute($attribute, $states);
}
