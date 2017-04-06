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
        return $this->workflow()->isInState(self::STATE_DRAFT, $this);
    }

    public function isPublished()
    {
        return $this->workflow()->isInState(self::STATE_PUBLISHED, $this);
    }

	public function isArchived()
	{
		return $this->workflow()->isInState(self::STATE_ARCHIVED, $this);
	}

	/**
     * Tests the attribute of the state. This is not a specific state, but an attribute of a state.
     */
    public function isActive()
    {
        return $this->workflow()->hasAttribute(self::ATTRIBUTE_ACTIVE);
    }

    public function moveToDraft()
    {
        $this->workflow()->transitContext(self::TRANSITION_TO_DRAFT, $this);
    }

    public function publish()
    {
        $this->workflow()->transitContext(self::TRANSITION_PUBLISH, $this);
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
	        ->allowTransition(self::TRANSITION_PUBLISH, self::STATE_DRAFT, self::STATE_PUBLISHED)
	        ->allowTransition(self::TRANSITION_TO_DRAFT, self::STATE_PUBLISHED, self::STATE_DRAFT)
	        ->create($this->state);
    }
}
