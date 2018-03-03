<?php

namespace Star\Component\State\Example;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Embeddable;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;
use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateMetadata;

/**
 * @Entity()
 */
class MyEntity
{
    /**
     * @var int
     * @Id()
     * @GeneratedValue(strategy="AUTO")
     * @Column(name="id", type="integer")
     */
    public $id;

    /**
     * @var MyState
     * @Embedded(class="MyState", columnPrefix="my_")
     */
    private $state;

    public function __construct()
    {
        $this->state = new MyState();
    }

    public function isLocked()
    {
        return $this->state->isInState('locked');
    }

    public function lock()
    {
        $this->state = $this->state->transit('lock', $this);
    }

    public function unlock()
    {
        $this->state = $this->state->transit('unlock', $this);
    }
}

/**
 * @Embeddable()
 */
final class MyState extends StateMetadata
{
    /**
     * @var string
     * @Column(name="state", type="string")
     */
    protected $current;

    public function __construct()
    {
        parent::__construct('locked');
    }

    protected function configure(StateBuilder $builder)
    {
        $builder->allowTransition('lock', 'unlocked', 'locked');
        $builder->allowTransition('unlock', 'locked', 'unlocked');
    }
}

final class DoctrineMappedContextTest extends TestCase
{
    /**
     * @var EntityManager
     */
    private $em;

    public function setUp()
    {
        $configuration = Setup::createAnnotationMetadataConfiguration([__DIR__], true);
        $this->em = EntityManager::create(
            [
                'driver' => 'pdo_sqlite',
                'in_memory' => true,
            ],
            $configuration
        );
        $tool = new SchemaTool($this->em);
        $tool->createSchema([$this->em->getClassMetadata(MyEntity::class)]);
    }

    public function tearDown()
    {
        $tool = new SchemaTool($this->em);
        $tool->dropDatabase();
    }

    public function test_it_should_keep_the_state_on_hydrate()
    {
        $beforePersist = new MyEntity();

        $this->assertTrue($beforePersist->isLocked(), 'Should be locked by default');
        $locked = $this->save($beforePersist);
        $this->assertTrue($locked->isLocked(), 'Should be locked after persist');

        $locked->unlock();

        $this->assertFalse($locked->isLocked(), 'Should be unlocked before persist');
        $unlocked = $this->save($locked);
        $this->assertFalse($unlocked->isLocked(), 'Should be unlocked after persist');

        $unlocked->lock();

        $this->assertTrue($unlocked->isLocked(), 'Should be locked before persist');
        $final = $this->save($unlocked);
        $this->assertTrue($final->isLocked(), 'Should be locked after persist');
    }

    /**
     * @param MyEntity
     *
     * @return MyEntity
     */
    private function save($entity)
    {
        $this->em->persist($entity);
        $this->em->flush();
        $id = $entity->id;
        $this->em->clear();

        return $this->em->find(MyEntity::class, $id);
    }
}
