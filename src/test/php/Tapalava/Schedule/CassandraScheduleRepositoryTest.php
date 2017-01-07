<?php

namespace Tapalava\Schedule;

use Cassandra;
use Cassandra\Collection;
use Cassandra\ExecutionOptions;
use Cassandra\Statement;
use DateTime;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use PHPUnit_Framework_TestCase as TestCase;
use Doctrine\Common\Collections\ArrayCollection as FakeCassandraRows;

class CassandraScheduleRepositoryTest extends TestCase
{
    /**
     * Repository should return in-tact models.
     *
     * @test
     */
    public function find()
    {
        $stubClient = new class extends Client {
            public function __construct() {}

            public function execute(Statement $statement, ExecutionOptions $options = null) {
                $tags = new Collection(Cassandra::TYPE_VARCHAR);
                $tags->add('a');
                $tags->add('b');

                $dates = new Collection(Cassandra::TYPE_VARCHAR);
                $dates->add('2016-04-01');
                $dates->add('2016-04-02');

                return new FakeCassandraRows([
                    [
                        'id' => 'fake-id-001',
                        'name' => 'fake-name',
                        'days' => $dates,
                        'description' => 'fake description',
                        'banner' => 'http://nope.city/.gif',
                        'location' => 'Springfield',
                        'tags' => $tags,
                    ]
                ]);
            }
        };
        $stubTransformer = new class extends DateCollectionTransformer {
            public function toArray(Collection $collection = null): ?array {
                return [new DateTime('2016-04-01'), new DateTime('2016-04-02')];
            }
            public function toCollection(array $dates = null): ?Collection {}
        };
        $repository = new CassandraScheduleRepository($stubClient, $stubTransformer);

        $test = $repository->find('fake-id-001');

        $this->assertNotNull($test);
        $this->assertEquals('fake-id-001', $test->getId());
        $this->assertEquals('fake-name', $test->getName());
        $this->assertEquals(2, count($test->getDays()));
        $this->assertEquals('2016-04-01', $test->getDays()[0]->format('Y-m-d'));
        $this->assertEquals('2016-04-02', $test->getDays()[1]->format('Y-m-d'));
        $this->assertEquals('fake description', $test->getDescription());
        $this->assertEquals('http://nope.city/.gif', $test->getBanner());
        $this->assertEquals('Springfield', $test->getLocation());
        $this->assertEquals(2, count($test->getTags()));
        $this->assertEquals('a', $test->getTags()[0]);
        $this->assertEquals('b', $test->getTags()[1]);
    }

    /**
     * If no row is found, the repository should throw exception.
     *
     * @test
     * @expectedException \Tapalava\Schedule\ScheduleNotFoundException
     */
    public function notFound()
    {
        $stubClient = new class extends Client {
            public function __construct() {}
            public function execute(Statement $statement, ExecutionOptions $options = null) {
                return new FakeCassandraRows([]);
            }
        };
        $stubTransformer = new class extends DateCollectionTransformer {
            public function toCollection(array $dates = null): ?Collection {}
            public function toArray(Collection $collection = null): ?array {}
        };
        $repository = new CassandraScheduleRepository($stubClient, $stubTransformer);

        $repository->find('missing-id');
    }

    /**
     * Querying all rows should result in multiple in-tact models.
     *
     * @test
     */
    public function findAll()
    {
        $stubClient = new class extends Client {
            public function __construct() {}

            public function execute(Statement $statement, ExecutionOptions $options = null) {
                $tags = new Collection(Cassandra::TYPE_VARCHAR);
                $tags->add('a');
                $tags->add('b');

                $dates = new Collection(Cassandra::TYPE_VARCHAR);
                $dates->add('2016-04-01');
                $dates->add('2016-04-02');

                return new FakeCassandraRows([
                    [
                        'id' => 'fake-id-001',
                        'name' => 'fake-name',
                        'days' => $dates,
                        'description' => 'fake description',
                        'banner' => 'http://nope.city/.gif',
                        'location' => 'Springfield',
                        'tags' => $tags,
                    ],
                    [
                        'id' => 'fake-id-002',
                    ],
                ]);
            }
        };
        $stubTransformer = new class extends DateCollectionTransformer {
            public function toArray(Collection $collection = null): ?array {
                return [new DateTime('2016-04-01'), new DateTime('2016-04-02')];
            }
            public function toCollection(array $dates = null): ?Collection {}
        };
        $repository = new CassandraScheduleRepository($stubClient, $stubTransformer);

        $results = $repository->findAll();

        $this->assertEquals(2, count($results));
        $test = $results[0];
        $test2 = $results[1];
        $this->assertNotNull($test);
        $this->assertNotNull($test2);
        $this->assertEquals('fake-id-001', $test->getId());
        $this->assertEquals('fake-id-002', $test2->getId());
        $this->assertEquals('fake-name', $test->getName());
        $this->assertEquals(2, count($test->getDays()));
        $this->assertEquals('2016-04-01', $test->getDays()[0]->format('Y-m-d'));
        $this->assertEquals('2016-04-02', $test->getDays()[1]->format('Y-m-d'));
        $this->assertEquals('fake description', $test->getDescription());
        $this->assertEquals('http://nope.city/.gif', $test->getBanner());
        $this->assertEquals('Springfield', $test->getLocation());
        $this->assertEquals(2, count($test->getTags()));
        $this->assertEquals('a', $test->getTags()[0]);
        $this->assertEquals('b', $test->getTags()[1]);
    }

    /**
     * Querying for all rows in an empty database should result in an empty collection.
     *
     * @test
     */
    public function findAllEmpty()
    {
        $stubClient = new class extends Client {
            public function __construct() {}

            public function execute(Statement $statement, ExecutionOptions $options = null) {
                return new FakeCassandraRows([]);
            }
        };
        $stubTransformer = new class extends DateCollectionTransformer {
            public function toArray(Collection $collection = null): ?array {
                return [];
            }
            public function toCollection(array $dates = null): ?Collection {}
        };
        $repository = new CassandraScheduleRepository($stubClient, $stubTransformer);

        $results = $repository->findAll();

        $this->assertNotNull($results);
        $this->assertEquals(0, count($results));
    }

    /**
     * Repository should be able to save models.
     *
     * This test uses a Spy to ensure that the Cassandra client receives data
     * that matches the model that was saved.
     *
     * @test
     */
    public function save()
    {
        $spyClient = new class($this) extends Client {
            private $test;
            public function __construct(TestCase $test) { $this->test = $test; }

            public function execute(Statement $statement, ExecutionOptions $options = null) {

                $arguments = $options->arguments;
                $this->test->assertEquals('fake-id-001', $arguments['id']);
                $this->test->assertEquals('Fake Schedule', $arguments['name']);
                $this->test->assertInstanceOf(Collection::class, $arguments['days']);
                $this->test->assertEquals('2016-12-15'  , $arguments['days']->get(0));
                $this->test->assertEquals('2016-12-16'  , $arguments['days']->get(1));
                $this->test->assertEquals('This is a description', $arguments['description']);
                $this->test->assertEquals('http://nope.city/.gif', $arguments['banner']);
                $this->test->assertEquals('Mars', $arguments['location']);
                $this->test->assertInstanceOf(Collection::class, $arguments['tags']);
                $this->test->assertEquals('a'  , $arguments['tags']->get(0));
                $this->test->assertEquals('b'  , $arguments['tags']->get(1));

                return new FakeCassandraRows([]);
            }
        };
        $stubTransformer = new class extends DateCollectionTransformer {
            public function toArray(Collection $collection = null): ?array {
                return [];
            }
            public function toCollection(array $dates = null): ?Collection {
                $dates = new Collection(Cassandra::TYPE_VARCHAR);
                $dates->add('2016-12-15');
                $dates->add('2016-12-16');

                return $dates;
            }
        };
        $repository = new CassandraScheduleRepository($spyClient, $stubTransformer);

        $data = new Schedule(
            'fake-id-001',
            'Fake Schedule',
            [
                new DateTime('2016-12-15'),
                new DateTime('2016-12-16'),
            ],
            'This is a description',
            'http://nope.city/.gif',
            'Mars',
            ['a', 'b']
        );

        $result = $repository->save($data);

        $this->assertEquals('fake-id-001', $result);
    }

    /**
     * Repository has the ability to save an empty model without error and will
     * generate an ID for it.
     *
     * @test
     */
    public function saveEmpty()
    {
        $spyClient = new class($this) extends Client {
            private $test;
            public function __construct(TestCase $test) { $this->test = $test; }

            public function execute(Statement $statement, ExecutionOptions $options = null) {

                $arguments = $options->arguments;
                $this->test->assertNotNull($arguments['id']);
                $this->test->assertNull($arguments['name']);
                $this->test->assertInstanceOf(Collection::class, $arguments['days']);
                $this->test->assertEquals(0, $arguments['days']->count());
                $this->test->assertNull($arguments['description']);
                $this->test->assertNull($arguments['banner']);
                $this->test->assertNull($arguments['location']);
                $this->test->assertInstanceOf(Collection::class, $arguments['tags']);
                $this->test->assertEquals(0 , $arguments['tags']->count());

                return new FakeCassandraRows([]);
            }
        };
        $stubTransformer = new class extends DateCollectionTransformer {
            public function toArray(Collection $collection = null): array {
                return [];
            }
            public function toCollection(array $dates = null): ?Collection {
                $dates = new Collection(Cassandra::TYPE_VARCHAR);

                return $dates;
            }
        };
        $repository = new CassandraScheduleRepository($spyClient, $stubTransformer);

        $data = new Schedule();

        $result = $repository->save($data);

        $this->assertNotNull($result);
    }
}
