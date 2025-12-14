<?php declare(strict_types=1);

namespace Star\Component\State\Example;

use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\RegistryBuilder;
use Star\Component\State\StateContext;
use Star\Component\State\StateMetadata;
use Star\Component\State\StateTransition;

final class ContextUsingCustomMetadataTest extends TestCase
{
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
     * * On publish: set published at
     * * On discard: set discarded by
     * * On post edit: move to pending (Override all rules)
     */

    public function test_it_should_allow_to_transit_from_pending_to_approved(): void
    {
        $context = new ContextStub();
        $this->assertTrue($context->state->isInState('pending'));

        $context->approve();

        $this->assertTrue($context->state->isInState('approved'));
    }

    public function test_it_should_allow_to_transit_from_pending_to_archived(): void
    {
        $context = new ContextStub();
        $this->assertTrue($context->state->isInState('pending'));

        $context->discard();

        $this->assertTrue($context->state->isInState('archived'));
    }

    public function test_it_should_allow_to_transit_from_approved_to_published(): void
    {
        $context = new ContextStub();
        $context->approve();
        $this->assertTrue($context->state->isInState('approved'));

        $context->publish();

        $this->assertTrue($context->state->isInState('published'));
    }

    public function test_it_should_allow_to_transit_from_approved_to_archived(): void
    {
        $context = new ContextStub();
        $context->approve();
        $this->assertTrue($context->state->isInState('approved'));

        $context->archive();

        $this->assertTrue($context->state->isInState('archived'));
    }

    public function test_it_should_allow_to_transit_from_published_to_approved(): void
    {
        $context = new ContextStub();
        $context->approve();
        $context->publish();
        $this->assertTrue($context->state->isInState('published'));

        $context->remove();

        $this->assertTrue($context->state->isInState('approved'));
    }

    public function test_it_should_allow_to_transit_from_published_to_archived(): void
    {
        $context = new ContextStub();
        $context->approve();
        $context->publish();
        $this->assertTrue($context->state->isInState('published'));

        $context->archive();

        $this->assertTrue($context->state->isInState('archived'));
    }

    public function test_it_should_allow_to_transit_from_archived_to_pending(): void
    {
        $context = new ContextStub();
        $context->discard();
        $this->assertTrue($context->state->isInState('archived'));

        $context->reOpen();

        $this->assertTrue($context->state->isInState('pending'));
    }

    public function test_it_should_allow_to_transit_from_archived_to_approved(): void
    {
        $context = new ContextStub();
        $context->discard();
        $this->assertTrue($context->state->isInState('archived'));

        $context->unArchive();

        $this->assertTrue($context->state->isInState('approved'));
    }

    public function test_attributes_of_pending(): void
    {
        $context = new ContextStub();
        $this->assertTrue($context->state->isInState('pending'));
        $this->assertTrue($context->isDraft());
        $this->assertFalse($context->isVisible());
    }

    public function test_attributes_of_approved(): void
    {
        $context = new ContextStub();
        $context->discard();
        $context->unArchive();

        $this->assertTrue($context->state->isInState('approved'));
        $this->assertTrue($context->isDraft());
        $this->assertFalse($context->isVisible());
    }

    public function test_attributes_of_published(): void
    {
        $context = new ContextStub();
        $context->approve();
        $context->publish();

        $this->assertTrue($context->state->isInState('published'));
        $this->assertFalse($context->isDraft());
        $this->assertTrue($context->isVisible());
    }

    public function test_attributes_of_archived(): void
    {
        $context = new ContextStub();
        $context->approve();
        $context->publish();
        $context->archive();

        $this->assertTrue($context->state->isInState('archived'));
        $this->assertFalse($context->isDraft());
        $this->assertFalse($context->isVisible());
    }
}

final class MyStateWorkflow extends StateMetadata
{
    public function __construct()
    {
        parent::__construct('pending');
    }

    protected function configure(StateBuilder $builder): void
    {
        $builder->allowTransition('approve', 'pending', 'approved');
        $builder->allowTransition('discard', 'pending', 'archived');
        $builder->allowTransition('publish', 'approved', 'published');
        $builder->allowTransition('remove', 'published', 'approved');
        $builder->allowTransition('archive', ['approved', 'published'], 'archived');
        $builder->allowTransition('un-archive', 'archived', 'approved');
        $builder->allowCustomTransition(new ReOpenTransition());
        $builder->addAttribute('is_visible', 'published');
        $builder->addAttribute('is_draft', ['pending', 'approved']);
    }
}

final class ReOpenTransition implements StateTransition
{
    public function getName(): string
    {
        return 're-open';
    }

    public function onRegister(RegistryBuilder $registry): void
    {
        $registry->registerStartingState('re-open', 'archived', []);
        $registry->registerDestinationState('re-open', 'pending', []);
    }

    public function getDestinationState(): string
    {
        return 'pending';
    }
}

final class ContextStub implements StateContext
{
    public MyStateWorkflow|StateMetadata $state;

    public function __construct()
    {
        $this->state = new MyStateWorkflow();
    }

    public function toStateContextIdentifier(): string
    {
        return 'ContextStub';
    }

    public function publish(): void
    {
        $this->state = $this->state->transit('publish', $this);
    }

    public function approve(): void
    {
        $this->state = $this->state->transit('approve', $this);
    }

    public function discard(): void
    {
        $this->state = $this->state->transit('discard', $this);
    }

    public function reOpen(): void
    {
        $this->state = $this->state->transit('re-open', $this);
    }

    public function remove(): void
    {
        $this->state = $this->state->transit('remove', $this);
    }

    public function unPublish(): void
    {
        $this->state = $this->state->transit('un-publish', $this);
    }

    public function archive(): void
    {
        $this->state = $this->state->transit('archive', $this);
    }

    public function unArchive(): void
    {
        $this->state = $this->state->transit('un-archive', $this);
    }

    public function isDraft(): bool
    {
        return $this->state->hasAttribute('is_draft');
    }

    public function isVisible(): bool
    {
        return $this->state->hasAttribute('is_visible');
    }
}
