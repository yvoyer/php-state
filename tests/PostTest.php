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

    public function test_post_should_be_published()
    {
        $post = Post::published();
        $this->assertPostIsPublished($post);

        return $post;
    }

    public function test_post_should_be_archived()
    {
        $post = Post::archived();
        $this->assertPostIsArchived($post);

        return $post;
    }

    public function test_post_should_be_deleted()
    {
        $post = Post::deleted();
        $this->assertPostIsDeleted($post);

        return $post;
    }

    /**
     * @depends test_post_should_be_draft
     */
    public function test_it_should_allow_from_draft_to_draft(Post $post)
    {
        $post->markAsDraft();
        $this->assertPostIsDraft($post);
    }

    /**
     * @depends test_post_should_be_draft
     */
    public function test_it_should_allow_from_draft_to_published(Post $post)
    {
        $post->publish();
        $this->assertPostIsPublished($post);
    }

    /**
     * @depends test_post_should_be_draft
     *
     * @expectedException        \Star\Component\State\InvalidGameTransitionException
     * @expectedExceptionMessage dasdsa
     */
    public function test_it_should_not_allow_from_draft_to_archived(Post $post)
    {
        $post->archive();
    }

    /**
     * @depends test_post_should_be_draft
     */
    public function test_it_should_allow_from_draft_to_deleted(Post $post)
    {
        $post->delete();
        $this->assertPostIsDeleted($post);
    }

    /**
     * @param Post $post
     */
    private function assertPostIsDraft(Post $post)
    {
        $this->assertTrue($post->isDraft());
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
        $this->assertTrue($post->isPublished());
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
        $this->assertTrue($post->isArchived());
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
        $this->assertTrue($post->isDeleted());
    }
}
