<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Example;

use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

/**
 * Example of usage when using self contained workflow creation.
 */
class Post implements StateContext
{
    const ALIAS = 'post';

    const STATE_DRAFT = 'drafted';
    const STATE_PUBLISHED = 'published';
    const STATE_ARCHIVED = 'archived';

    const TRANSITION_PUBLISH = 'publish';
    const TRANSITION_TO_DRAFT = 'to_draft';
    const TRANSITION_ARCHIVE = 'archive';

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
        return $this->workflow()->isInState(self::STATE_DRAFT);
    }

    public function isPublished()
    {
        return $this->workflow()->isInState(self::STATE_PUBLISHED);
    }

    public function isArchived()
    {
        return $this->workflow()->isInState(self::STATE_ARCHIVED);
    }

    public function isActive()
    {
        return $this->workflow()->hasAttribute(self::ATTRIBUTE_ACTIVE);
    }

    public function isClosed()
    {
        return $this->workflow()->hasAttribute(self::ATTRIBUTE_CLOSED);
    }

    public function moveToDraft()
    {
        $this->state = $this->workflow()->transitContext(self::TRANSITION_TO_DRAFT, $this);
    }

    public function publish()
    {
        $this->state = $this->workflow()->transitContext(self::TRANSITION_PUBLISH, $this);
    }

    public function archive()
    {
        $this->state = $this->workflow()->transitContext(self::TRANSITION_ARCHIVE, $this);
    }

    /**
     * @return Post
     */
    public static function drafted()
    {
        return new self(self::STATE_DRAFT);
    }

    /**
     * @return Post
     */
    public static function published()
    {
        return new self(self::STATE_PUBLISHED);
    }

    /**
     * @return Post
     */
    public static function archived()
    {
        return new self(self::STATE_ARCHIVED);
    }

    /**
     * @return StateMachine
     */
    private function workflow()
    {
        /**
         * Transitions
         * +------------+------------+------------+------------+
         * | from / to  |    draft   | published  |  archived  |
         * +============+============+============+============+
         * | draft      | disallowed | publish    | disallowed |
         * +------------+------------+------------+------------+
         * | published  | disallowed | disallowed | archive    |
         * +------------+------------+------------+------------+
         * | archived   | disallowed | to_draft   | disallowed |
         * +------------+------------+------------+------------+
         *
         * Attributes
         * +-------------------+------------+------------+
         * | state / attribute | is_active  | is_closed  |
         * +===================+============+============+
         * | draft             |   false    |   true     |
         * +-------------------+------------+------------+
         * | published         |   true     |   false    |
         * +-------------------+------------+------------+
         * | archived          |   false    |   true     |
         * +-------------------+------------+------------+
         */
        return StateBuilder::build()
            ->allowTransition(self::TRANSITION_PUBLISH, self::STATE_DRAFT, self::STATE_PUBLISHED)
            ->allowTransition(self::TRANSITION_TO_DRAFT, self::STATE_PUBLISHED, self::STATE_DRAFT)
            ->allowTransition(self::TRANSITION_ARCHIVE, self::STATE_PUBLISHED, self::STATE_ARCHIVED)
            ->addAttribute(self::ATTRIBUTE_ACTIVE, self::STATE_PUBLISHED)
            ->addAttribute(self::ATTRIBUTE_CLOSED, [self::STATE_ARCHIVED, self::STATE_DRAFT])
            ->create($this->state);
    }
}
