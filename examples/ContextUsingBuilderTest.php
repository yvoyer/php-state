<?php declare(strict_types=1);

namespace Star\Component\State\Example;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\InvalidStateTransitionException;
use Star\Component\State\StateMachine;

final class ContextUsingBuilderTest extends TestCase
{
    public function test_post_should_be_draft(): void
    {
        $post = Post::drafted();
        $this->assertTrue($post->isDraft());
        $this->assertFalse($post->isPublished());
        $this->assertFalse($post->isArchived());
    }

    public function test_post_should_be_published(): void
    {
        $post = Post::published();
        $this->assertFalse($post->isDraft());
        $this->assertTrue($post->isPublished());
        $this->assertFalse($post->isArchived());
    }

    public function test_post_should_be_archived(): void
    {
        $post = Post::archived();
        $this->assertFalse($post->isDraft());
        $this->assertFalse($post->isPublished());
        $this->assertTrue($post->isArchived());
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     */
    public function test_it_should_not_allow_from_draft_to_draft(): void
    {
        $post = Post::drafted();
        $this->assertTrue($post->isDraft());

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 'to_draft' is not allowed when context 'post' is in state 'drafted'."
        );
        $post->moveToDraft();
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     */
    public function test_it_should_allow_from_draft_to_published(): void
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
     */
    public function test_it_should_not_allow_from_published_to_published(): void
    {
        $post = Post::published();
        $this->assertTrue($post->isPublished());

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 'publish' is not allowed when context 'post' is in state 'published'."
        );
        $post->publish();
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     */
    public function test_it_should_allow_from_published_to_draft(): void
    {
        $post = Post::published();
        $this->assertTrue($post->isPublished());

        $post->moveToDraft();

        $this->assertTrue($post->isDraft());
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_not_allow_from_draft_to_archived(): void
    {
        $post = Post::drafted();
        $this->assertTrue($post->isDraft());

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 'archive' is not allowed when context 'post' is in state 'drafted'."
        );
        $post->archive();
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_allow_from_published_to_archived(): void
    {
        $post = Post::published();
        $this->assertTrue($post->isPublished());

        $post->archive();

        $this->assertTrue($post->isArchived());
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_not_allow_from_archived_to_archived(): void
    {
        $post = Post::archived();
        $this->assertTrue($post->isArchived());

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 'archive' is not allowed when context 'post' is in state 'archived'."
        );
        $post->archive();
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_not_allow_from_archived_to_draft(): void
    {
        $post = Post::archived();
        $this->assertTrue($post->isArchived());

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 'to_draft' is not allowed when context 'post' is in state 'archived'."
        );
        $post->moveToDraft();
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_not_allow_from_archived_to_published(): void
    {
        $post = Post::archived();
        $this->assertTrue($post->isArchived());

        $this->expectException(InvalidStateTransitionException::class);
        $this->expectExceptionMessage(
            "The transition 'publish' is not allowed when context 'post' is in state 'archived'."
        );
        $post->publish();
    }

    /**
     * @depends test_post_should_be_draft
     * @depends test_post_should_be_published
     * @depends test_post_should_be_archived
     */
    public function test_it_should_allow_to_define_attributes_on_state(): void
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
final class Post
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

    private function __construct(string $state)
    {
        $this->state = $state;
    }

    public function isDraft(): bool
    {
        return $this->workflow()->isInState(self::STATE_DRAFT);
    }

    public function isPublished(): bool
    {
        return $this->workflow()->isInState(self::STATE_PUBLISHED);
    }

    public function isArchived(): bool
    {
        return $this->workflow()->isInState(self::STATE_ARCHIVED);
    }

    public function isActive(): bool
    {
        return $this->workflow()->hasAttribute(self::ATTRIBUTE_ACTIVE);
    }

    public function isClosed(): bool
    {
        return $this->workflow()->hasAttribute(self::ATTRIBUTE_CLOSED);
    }

    public function moveToDraft(): void
    {
        $this->state = $this->workflow()->transit(self::TRANSITION_TO_DRAFT, 'post');
    }

    public function publish(): void
    {
        $this->state = $this->workflow()->transit(self::TRANSITION_PUBLISH, 'post');
    }

    public function archive(): void
    {
        $this->state = $this->workflow()->transit(self::TRANSITION_ARCHIVE, 'post');
    }

    /**
     * @return Post
     */
    public static function drafted()
    {
        return new self(self::STATE_DRAFT);
    }

    public static function published(): self
    {
        return new self(self::STATE_PUBLISHED);
    }

    public static function archived(): self
    {
        return new self(self::STATE_ARCHIVED);
    }

    private function workflow(): StateMachine
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
