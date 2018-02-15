<?php
/**
 * This file is part of the php-state project.
 *
 * (c) Yannick Voyer <star.yvoyer@gmail.com> (http://github.com/yvoyer)
 */

namespace Star\Component\State\Example;

final class PostTest extends \PHPUnit_Framework_TestCase
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
