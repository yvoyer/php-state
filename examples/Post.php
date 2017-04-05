<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Example;

use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\State;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

/**
 * Example of usage when using self contained workflow creation.
 */
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
    const TRANSITION_TO_DRAFT = 'to_draft';
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
        return $this->workflow()->is(self::DRAFT, $this);
    }

    public function isPublished()
    {
        return $this->workflow()->is(self::PUBLISHED, $this);
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

    public function moveToDraft()
    {
        $this->workflow()->transitContext(self::TRANSITION_TO_DRAFT, $this);
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

    /**
     * @return StateMachine
     */
    private function workflow()
    {
        return StateBuilder::build()
	        ->allowTransition(self::TRANSITION_PUBLISH, self::DRAFT, self::PUBLISHED)
	        ->allowTransition(self::TRANSITION_TO_DRAFT, self::PUBLISHED, self::DRAFT)
	        ->create($this->state);
//            ->oneToOne(self::ALIAS, self::TRANSITION_PUBLISH, self::DRAFT, self::PUBLISHED)
  //          ->oneToOne(self::ALIAS, self::TRANSITION_DUMP, self::DRAFT, self::DELETED)
    //        ->oneToOne(self::ALIAS, self::TRANSITION_ARCHIVE, self::PUBLISHED, self::ARCHIVED)
       //     ->oneToOne(self::ALIAS, self::TRANSITION_DELETE, self::ARCHIVED, self::DELETED)
         //   ;
    }
}
