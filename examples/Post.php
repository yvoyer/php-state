<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Example;

use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

class Post implements StateContext
{
    const DRAFT = 'draft';
    const PUBLISHED = 'published';
    const ARCHIVED = 'archived';
    const DELETED = 'deleted';

    const ATTRIBUTE_ACTIVE = 'active';
    const ATTRIBUTE_CLOSED = 'closed';

    /**
     * @var string
     */
    private $state;

    private function __construct($state)
    {
        $this->state = $state;
    }

    public function isDraft()
    {
        return $this->workflow()->isState(self::DRAFT, $this);
    }

    public function isPublished()
    {
        return $this->workflow()->isState(self::PUBLISHED, $this);
    }

    public function isArchived()
    {
        return $this->workflow()->isState(self::ARCHIVED, $this);
    }

    public function isDeleted()
    {
        return $this->workflow()->isState(self::DELETED, $this);
    }

    /**
     * Tests the attribute of the state. This is not a specific state, but an attribute of a state.
     */
    public function isActive()
    {
        return $this->workflow()->hasAttribute(self::ATTRIBUTE_ACTIVE);
    }

    public function markAsDraft()
    {
        $this->workflow()->transitContext($this, self::DRAFT);
    }

    public function publish()
    {
        $this->workflow()->transitContext($this, self::PUBLISHED);
    }

    public function archive()
    {
        $this->workflow()->transitContext($this, self::ARCHIVED);
    }

    public function delete()
    {
        $this->workflow()->transitContext($this, self::DELETED);
    }

    /**
     * @return Post
     */
    public static function draft()
    {
        return new self(self::DRAFT);
    }

    /**
     * @return Post
     */
    public static function published()
    {
        return new self(self::PUBLISHED);
    }

    /**
     * @return Post
     */
    public static function archived()
    {
        return new self(self::ARCHIVED);
    }

    /**
     * @return Post
     */
    public static function deleted()
    {
        return new self(self::DELETED);
    }

    public function setState(State $state)
    {
        $this->state = $state->toString();
    }

    public function getCurrentState()
    {
        return $this->workflow()->state($this->state);
    }

    public function contextAlias()
    {
        return 'post';
    }

    /**
     * @return StateMachine
     */
    private function workflow()
    {
        return StateMachine::create($this)
            ->whitelist(self::DRAFT, [self::PUBLISHED, self::DELETED])
            ->whitelist(self::PUBLISHED, [self::DRAFT, self::ARCHIVED])
            ->whitelist(self::ARCHIVED, [self::DRAFT, self::DELETED])
            ->addAttribute(self::ATTRIBUTE_CLOSED, [self::ARCHIVED, self::DELETED])
            ->addAttributes(self::PUBLISHED, [self::ATTRIBUTE_ACTIVE])
            // deleted post cannot have transitions
            ;
    }
}
