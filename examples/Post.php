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
    const TRANSITION_PUBLISH = 'publish';
    const ALIAS = 'post';
    const TRANSITION_DELETE = 'delete';
    const TRANSITION_DUMP = 'dump';
    const TRANSITION_ARCHIVE = 'archive';
    const TRANSITION_UNPUBLISH = 'unPublish';
    const TRANSITION_UNARCHIVE = 'unArchive';

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
        return $this->workflow()->hasAttribute(self::ATTRIBUTE_ACTIVE, $this);
    }

    public function dump()
    {
        $this->workflow()->transitContext(self::TRANSITION_DUMP, $this);
    }

    public function publish()
    {
        $this->workflow()->transitContext(self::TRANSITION_PUBLISH, $this);
    }

    public function archive()
    {
        $this->workflow()->transitContext(self::TRANSITION_ARCHIVE, $this);
    }

    public function delete()
    {
        $this->workflow()->transitContext(self::TRANSITION_DELETE, $this);
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
        $this->state = $state->name();
    }

    public function getCurrentState()
    {
        return $this->workflow()->getState($this->state, self::ALIAS);
    }

    public function contextAlias()
    {
        return self::ALIAS;
    }

    /**
     * @return StateMachine
     */
    public static function workflow()
    {
        return StateMachine::create()
//            ->oneToOne(self::ALIAS, self::TRANSITION_ARCHIVE, self::DRAFT, self::ARCHIVED)
//            ->oneToOne(self::ALIAS, self::TRANSITION_DUMP, self::DRAFT, self::DRAFT)
            ->oneToOne(self::ALIAS, self::TRANSITION_PUBLISH, self::DRAFT, self::PUBLISHED)
            ->oneToOne(self::ALIAS, self::TRANSITION_DUMP, self::DRAFT, self::DELETED)
            ->oneToOne(self::ALIAS, self::TRANSITION_ARCHIVE, self::PUBLISHED, self::ARCHIVED)
//            ->oneToOne(self::ALIAS, self::TRANSITION_UNPUBLISH, self::PUBLISHED, self::DRAFT)
//            ->oneToOne(self::ALIAS, self::TRANSITION_UNARCHIVE, self::ARCHIVED, self::DRAFT)
//            ->oneToOne(self::ALIAS, self::TRANSITION_DELETE, self::DRAFT, self::DELETED)
            ->oneToOne(self::ALIAS, self::TRANSITION_DELETE, self::ARCHIVED, self::DELETED)
//            ->addAttribute(self::ALIAS, self::PUBLISHED, self::ATTRIBUTE_ACTIVE)
//            ->addAttribute(self::ALIAS, [self::ARCHIVED, self::DELETED], self::ATTRIBUTE_CLOSED)
            // deleted post cannot have transitions
            ;
    }
}
