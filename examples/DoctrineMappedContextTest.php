<?php declare(strict_types=1);

namespace Star\Component\State\Example;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Star\Component\State\Builder\StateBuilder;
use Star\Component\State\StateMetadata;

final class DoctrineMappedContextTest extends TestCase
{
    private EntityManagerInterface $em;

    public function setUp(): void
    {
        if (!\extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Sqlite extension is needed');
        }

        $configuration = ORMSetup::createAttributeMetadataConfiguration([__DIR__], true);
        $connection = DriverManager::getConnection(
            [
                'driver' => 'pdo_sqlite',
                'in_memory' => true,
            ],
            $configuration,
        );
        $this->em = new EntityManager($connection, $configuration);
        $tool = new SchemaTool($this->em);
        $tool->createSchema([$this->em->getClassMetadata(MyEntity::class)]);
    }

    public function tearDown(): void
    {
        $tool = new SchemaTool($this->em);
        $tool->dropDatabase();
    }

    public function test_it_should_keep_the_state_on_hydrate(): void
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

    private function save(MyEntity $entity): MyEntity
    {
        $this->em->persist($entity);
        $this->em->flush();
        $id = $entity->id;
        $this->em->clear();

        $refreshed = $this->em->find(MyEntity::class, $id);
        if (!$refreshed instanceof MyEntity) {
            throw new AssertionFailedError('entity not found');
        }

        return $refreshed;
    }
}

#[ORM\Entity]
class MyEntity
{
    #[ORM\Id()]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "id", type: "integer")]
    public int $id;

    #[ORM\Embedded(class: "MyState", columnPrefix: "my_")]
    private MyState $state;

    public function __construct()
    {
        $this->state = new MyState();
    }

    public function isLocked(): bool
    {
        return $this->state->isInState('locked');
    }

    public function lock(): void
    {
        $this->state = $this->state->transit('lock', $this);
    }

    public function unlock(): void
    {
        $this->state = $this->state->transit('unlock', $this);
    }
}

#[ORM\Embeddable]
final class MyState extends StateMetadata
{
    #[ORM\Column(name: "state", type: "string")]
    protected string $current;

    public function __construct()
    {
        parent::__construct('locked');
    }

    protected function configure(StateBuilder $builder): void
    {
        $builder->allowTransition('lock', 'unlocked', 'locked');
        $builder->allowTransition('unlock', 'locked', 'unlocked');
    }
}
