<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Example;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateContext;
use Star\Component\State\StateMachine;

final class ContextUsingBuilderTest extends TestCase
{
    public function test_post_should_be_draft()
    {
        $post = Post::drafted();
        $this->assertTrue($post->isDraft());
        $this->assertFalse($post->isPublished());
        $this->assertFalse($post->isArchived());
    }

    public function test_post_should_be_published()
    {
        $post = Post::published();
        $this->assertFalse($post->isDraft());
        $this->assertTrue($post->isPublished());
        $this->assertFalse($post->isArchived());
    }

    public function test_post_should_be_archived()
    {
        $post = Post::archived();
        $this->assertFalse($post->isDraft());
        $this->assertFalse($post->isPublished());
        $this->assertTrue($post->isArchived());
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'to_draft' is not allowed when context 'Star\Component\State\Example\Post' is in state 'drafted'.
     */
    public function test_it_should_not_allow_from_draft_to_draft()
    {
        $post = Post::drafted();
        $this->assertTrue($post->isDraft());

        $post->moveToDraft();
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     */
    public function test_it_should_allow_from_draft_to_published()
    {
        $post = Post::drafted();
        $this->assertTrue($post->isDraft());

        $post->publish();

        $this->assertFalse($post->isDraft());
        $this->assertTrue($post->isPublished());
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'publish' is not allowed when context 'Star\Component\State\Example\Post' is in state 'published'.
     */
    public function test_it_should_not_allow_from_published_to_published()
    {
        $post = Post::published();
        $this->assertTrue($post->isPublished());

        $post->publish();
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     */
    public function test_it_should_allow_from_published_to_draft()
    {
        $post = Post::published();
        $this->assertTrue($post->isPublished());

        $post->moveToDraft();

        $this->assertTrue($post->isDraft());
    }

    /**
     * @depends test_post_should_be_archived
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'archive' is not allowed when context 'Star\Component\State\Example\Post' is in state 'drafted'.
     */
    public function test_it_should_not_allow_from_draft_to_archived()
    {
        $post = Post::drafted();
        $this->assertTrue($post->isDraft());

        $post->archive();
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_allow_from_published_to_archived()
    {
        $post = Post::published();
        $this->assertTrue($post->isPublished());

        $post->archive();

        $this->assertTrue($post->isArchived());
    }

    /**
     * @depends test_post_should_be_archived
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'archive' is not allowed when context 'Star\Component\State\Example\Post' is in state 'archived'.
     */
    public function test_it_should_not_allow_from_archived_to_archived()
    {
        $post = Post::archived();
        $this->assertTrue($post->isArchived());

        $post->archive();
    }

    /**
     * @depends test_post_should_be_archived
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'to_draft' is not allowed when context 'Star\Component\State\Example\Post' is in state 'archived'.
     */
    public function test_it_should_not_allow_from_archived_to_draft()
    {
        $post = Post::archived();
        $this->assertTrue($post->isArchived());

        $post->moveToDraft();
    }

    /**
     * @depends test_post_should_be_archived
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition 'publish' is not allowed when context 'Star\Component\State\Example\Post' is in state 'archived'.
     */
    public function test_it_should_not_allow_from_archived_to_published()
    {
        $post = Post::archived();
        $this->assertTrue($post->isArchived());

        $post->publish();
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     * @depends test_post_should_be_archived
     */
    public function test_it_should_allow_to_define_attributes_on_state()
    {
        $this->assertFalse(Post::drafted()->isActive());
        $this->assertTrue(Post::published()->isActive());
        $this->assertFalse(Post::archived()->isActive());

        $this->assertTrue(Post::drafted()->isClosed());
        $this->assertFalse(Post::published()->isClosed());
        $this->assertTrue(Post::archived()->isClosed());
    }
}

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
        return StateBuilder::build()
            ->allowTransition(self::TRANSITION_PUBLISH, self::STATE_DRAFT, self::STATE_PUBLISHED)
            ->allowTransition(self::TRANSITION_TO_DRAFT, self::STATE_PUBLISHED, self::STATE_DRAFT)
            ->allowTransition(self::TRANSITION_ARCHIVE, self::STATE_PUBLISHED, self::STATE_ARCHIVED)
            ->addAttribute(self::ATTRIBUTE_ACTIVE, self::STATE_PUBLISHED)
            ->addAttribute(self::ATTRIBUTE_CLOSED, [self::STATE_ARCHIVED, self::STATE_DRAFT])
            ->create($this->state);
    }
}
