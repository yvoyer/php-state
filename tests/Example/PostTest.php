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
        $post = Post::draft();
        $this->assertPostIsDraft($post);

        return $post;
    }

    /**
     * @depends test_post_should_be_draft
     */
    public function test_it_should_allow_from_draft_to_draft()
    {
        $post = Post::draft();
        $post->dump();
        $this->assertPostIsDraft($post);
    }

    /**
     * @depends test_post_should_be_draft
     */
    public function test_it_should_allow_from_draft_to_published()
    {
        $post = Post::draft();
        $post->publish();
        $this->assertPostIsPublished($post);
    }

    /**
     * @depends test_post_should_be_draft
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition from 'draft' to 'archived' is not allowed.
     */
    public function test_it_should_not_allow_from_draft_to_archived()
    {
        $post = Post::draft();
        $post->archive();
    }

    /**
     * @depends test_post_should_be_draft
     */
    public function test_it_should_allow_from_draft_to_deleted()
    {
        $post = Post::draft();
        $post->delete();
        $this->assertPostIsDeleted($post);
    }

    public function test_post_should_be_published()
    {
        $post = Post::published();
        $this->assertPostIsPublished($post);

        return $post;
    }

    /**
     * @depends test_post_should_be_published
     */
    public function test_it_should_allow_from_publish_to_draft()
    {
        $post = Post::published();
        $post->dump();
        $this->assertPostIsDraft($post);
    }

    /**
     * @depends test_post_should_be_published
     */
    public function test_it_should_allow_from_publish_to_published()
    {
        $post = Post::published();
        $post->publish();
        $this->assertPostIsPublished($post);
    }

    /**
     * @depends test_post_should_be_published
     */
    public function test_it_should_allow_from_publish_to_archived()
    {
        $post = Post::published();
        $post->archive();
        $this->assertPostIsArchived($post);
    }

    /**
     * @depends test_post_should_be_published
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition from 'published' to 'deleted' is not allowed.
     */
    public function test_it_should_not_allow_from_publish_to_deleted()
    {
        $post = Post::published();
        $post->delete();
        $this->assertPostIsDeleted($post);
    }

    public function test_post_should_be_archived()
    {
        $post = Post::archived();
        $this->assertPostIsArchived($post);

        return $post;
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_allow_from_archived_to_draft()
    {
        $post = Post::archived();
        $post->dump();
        $this->assertPostIsDraft($post);
    }

    /**
     * @depends test_post_should_be_archived
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition from 'archived' to 'published' is not allowed.
     */
    public function test_it_should_not_allow_from_archived_to_published()
    {
        $post = Post::archived();
        $post->publish();
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_allow_from_archived_to_archived()
    {
        $post = Post::archived();
        $post->archive();
        $this->assertPostIsArchived($post);
    }

    /**
     * @depends test_post_should_be_archived
     */
    public function test_it_should_allow_from_archived_to_deleted()
    {
        $post = Post::archived();
        $post->delete();
        $this->assertPostIsDeleted($post);
    }

    public function test_post_should_be_deleted()
    {
        $post = Post::deleted();
        $this->assertPostIsDeleted($post);

        return $post;
    }

    /**
     * @depends test_post_should_be_deleted
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition from 'deleted' to 'draft' is not allowed.
     */
    public function test_it_should_not_allow_from_deleted_to_draft()
    {
        $post = Post::deleted();
        $post->dump();
    }

    /**
     * @depends test_post_should_be_deleted
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition from 'deleted' to 'published' is not allowed.
     */
    public function test_it_should_not_allow_from_deleted_to_published()
    {
        $post = Post::deleted();
        $post->publish();
    }

    /**
     * @depends test_post_should_be_deleted
     *
     * @expectedException        \Star\Component\State\InvalidStateTransitionException
     * @expectedExceptionMessage The transition from 'deleted' to 'archived' is not allowed.
     */
    public function test_it_should_not_allow_from_deleted_to_archived()
    {
        $post = Post::deleted();
        $post->archive();
    }

    /**
     * @depends test_post_should_be_deleted
     */
    public function test_it_should_allow_from_deleted_to_deleted()
    {
        $post = Post::deleted();
        $post->delete();
        $this->assertPostIsDeleted($post);
    }

    public function test_it_should_consider_the_states_as_active_using_attributes()
    {
        $this->assertTrue(Post::draft()->isActive());
        $this->assertTrue(Post::published()->isActive());
        $this->assertTrue(Post::archived()->isActive());
        $this->assertTrue(Post::deleted()->isActive());
    }

    /**
     * @param Post $post
     */
    private function assertPostIsDraft(Post $post)
    {
        $this->assertTrue($post->isDraft(), 'Post should be draft');
        $this->assertFalse($post->isPublished());
        $this->assertFalse($post->isArchived());
        $this->assertFalse($post->isDeleted());
    }

    /**
     * @param Post $post
     */
    private function assertPostIsPublished(Post $post)
    {
        $this->assertFalse($post->isDraft());
        $this->assertTrue($post->isPublished(), 'Post should be published');
        $this->assertFalse($post->isArchived());
        $this->assertFalse($post->isDeleted());
    }

    /**
     * @param Post $post
     */
    private function assertPostIsArchived(Post $post)
    {
        $this->assertFalse($post->isDraft());
        $this->assertFalse($post->isPublished());
        $this->assertTrue($post->isArchived(), 'Post should be archived');
        $this->assertFalse($post->isDeleted());
    }

    /**
     * @param Post $post
     */
    private function assertPostIsDeleted(Post $post)
    {
        $this->assertFalse($post->isDraft());
        $this->assertFalse($post->isPublished());
        $this->assertFalse($post->isArchived());
        $this->assertTrue($post->isDeleted(), 'Post should be deleted');
    }
}
