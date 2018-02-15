<?php

namespace Star\Component\State;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;

final class ContextUsingCustomMetadataTest extends TestCase {
    /**
     * +---------------------------------------------------------+
     * |                           Transition                    |
     * +-----------+---------+------------+-----------+----------+
     * | From / To | pending | approved   | published | archived |
     * +-----------+---------+------------+-----------+----------+
     * | pending   |   N/A   |  approve   |    N/A    | discard  |
     * +-----------+---------+------------+-----------+----------+
     * | approved  |   N/A   |    N/A     |  publish  | archive  |
     * +-----------+---------+------------+-----------+----------+
     * | published |   N/A   |   remove   |    N/A    | archive  |
     * +-----------+---------+------------+-----------+----------+
     * | archived  | re-open | un-archive |    N/A    |    N/A   |
     * +-----------+---------+------------+-----------+----------+
     *
     * +-----------------------------------+
     * |           |       Attributes      |
     * +-----------+----------+------------+
     * | State     | is_draft | is_visible |
     * +-----------+----------+------------+
     * | pending   |   true   |   false    |
     * +-----------+----------+------------+
     * | approved  |   true   |   false    |
     * +-----------+----------+------------+
     * | published |   false  |   true     |
     * +-----------+----------+------------+
     * | archived  |   false  |   false    |
     * +-----------+----------+------------+
     *
     * * On publish: set publisehd at
     * * On discard: set discarded by
     * * On post edit: move to pending (Override all rules)
     */
    // todo implement StateTester (with metadata)

    public function test_it_should_allow_to_transit_from_pending_to_approved()
    {
        $context = new ContextStub();
        $this->assertTrue($context->state->isInState('pending'));

        $context->approve();

        $this->assertTrue($context->state->isInState('approved'));
    }

    public function test_it_should_allow_to_transit_from_pending_to_archived()
    {
        $context = new ContextStub();
        $this->assertTrue($context->state->isInState('pending'));

        $context->discard();

        $this->assertTrue($context->state->isInState('archived'));
    }

    public function test_it_should_allow_to_transit_from_approved_to_published()
    {
        $context = new ContextStub();
        $context->approve();
        $this->assertTrue($context->state->isInState('approved'));

        $context->publish();

        $this->assertTrue($context->state->isInState('published'));

        return $context;
    }

    public function test_it_should_allow_to_transit_from_approved_to_archived()
    {
        $context = new ContextStub();
        $context->approve();
        $this->assertTrue($context->state->isInState('approved'));

        $context->archive();

        $this->assertTrue($context->state->isInState('archived'));
    }

    public function test_it_should_allow_to_transit_from_published_to_approved()
    {
        $context = new ContextStub();
        $context->approve();
        $context->publish();
        $this->assertTrue($context->state->isInState('published'));

        $context->remove();

        $this->assertTrue($context->state->isInState('approved'));
    }

    public function test_it_should_allow_to_transit_from_published_to_archived()
    {
        $context = new ContextStub();
        $context->approve();
        $context->publish();
        $this->assertTrue($context->state->isInState('published'));

        $context->archive();

        $this->assertTrue($context->state->isInState('archived'));

        return $context;
    }

    public function test_it_should_allow_to_transit_from_archived_to_pending()
    {
        $context = new ContextStub();
        $context->discard();
        $this->assertTrue($context->state->isInState('archived'));

        $context->reOpen();

        $this->assertTrue($context->state->isInState('pending'));
    }

    public function test_it_should_allow_to_transit_from_archived_to_approved()
    {
        $context = new ContextStub();
        $context->discard();
        $this->assertTrue($context->state->isInState('archived'));

        $context->unArchive();

        $this->assertTrue($context->state->isInState('approved'));

        return $context;
    }

    public function test_attributes_of_pending()
    {
        $context = new ContextStub();
        $this->assertTrue($context->state->isInState('pending'));
        $this->assertTrue($context->isDraft());
        $this->assertFalse($context->isVisible());
    }

    /**
     * @param ContextStub $context
     * @depends test_it_should_allow_to_transit_from_archived_to_approved
     */
    public function test_attributes_of_approved(ContextStub $context)
    {
        $this->assertTrue($context->state->isInState('approved'));
        $this->assertTrue($context->isDraft());
        $this->assertFalse($context->isVisible());
    }

    /**
     * @param ContextStub $context
     * @depends test_it_should_allow_to_transit_from_approved_to_published
     */
    public function test_attributes_of_published(ContextStub $context)
    {
        $this->assertTrue($context->state->isInState('published'));
        $this->assertFalse($context->isDraft());
        $this->assertTrue($context->isVisible());
    }

    /**
     * @param ContextStub $context
     * @depends test_it_should_allow_to_transit_from_published_to_archived
     */
    public function test_attributes_of_archived(ContextStub $context)
    {
        $this->assertTrue($context->state->isInState('archived'));
        $this->assertFalse($context->isDraft());
        $this->assertFalse($context->isVisible());
    }
}

final class ContextStub implements StateContext
{
    public $state;

    public function __construct()
    {
        $this->state = StateBuilder::build()
            ->allowTransition('approve', 'pending', 'approved')
            ->allowTransition('discard', 'pending', 'archived')
            ->allowTransition('publish', 'approved', 'published')
            ->allowTransition('remove', 'published', 'approved')
            ->allowTransition('archive', ['approved', 'published'], 'archived')
            ->allowTransition('un-archive', 'archived', 'approved')
            ->allowTransition('re-open', 'archived', 'pending')
//            ->addAttribute('is_visible', 'published')
//            ->addAttribute('is_draft', ['pending', 'approved'])
            ->create('pending');
    }

    public function publish()
    {
        $this->state = $this->state->transit('publish', $this);
    }

    public function approve()
    {
        $this->state = $this->state->transit('approve', $this);
    }

    public function discard()
    {
        $this->state = $this->state->transit('discard', $this);
    }

    public function reOpen()
    {
        $this->state = $this->state->transit('re-open', $this);
    }

    public function remove()
    {
        $this->state = $this->state->transit('remove', $this);
    }

    public function unPublish()
    {
        $this->state = $this->state->transit('un-publish', $this);
    }

    public function archive()
    {
        $this->state = $this->state->transit('archive', $this);
    }

    public function unArchive()
    {
        $this->state = $this->state->transit('un-archive', $this);
    }

    public function isDraft()
    {
        return $this->state->hasAttribute('is_draft');
    }

    public function isVisible()
    {
        return $this->state->hasAttribute('is_visible');
    }
}

final class StateMetadata
{
}
