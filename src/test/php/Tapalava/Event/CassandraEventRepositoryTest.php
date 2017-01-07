<?php

namespace Tapalava\Event;

use Cassandra;
use Cassandra\Collection;
use Cassandra\ExecutionOptions;
use Cassandra\Statement;
use DateTime;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use PHPUnit_Framework_TestCase as TestCase;
use Doctrine\Common\Collections\ArrayCollection as FakeCassandraRows;

class CassandraEventRepositoryTest extends TestCase
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

                $hosts = new Collection(Cassandra::TYPE_VARCHAR);
                $hosts->add('jane');
                $hosts->add('john');

                return new FakeCassandraRows([
                    [
                        'id' => 'fake-id-001',
                        'schedule_id' => 'fake-schedule-id',
                        'name' => 'fake-name',
                        'start' =>  new Cassandra\Timestamp(1460225960),
                        'end' =>  new Cassandra\Timestamp(1462814299),
                        'category' => 'fake category',
                        'tags' => $tags,
                        'room' => 'fake room',
                        'hosts' => $hosts,
                        'description' => 'fake description',
                        'banner' => 'http://nope.city/.gif',
                    ]
                ]);
            }
        };
        $repository = new CassandraEventRepository($stubClient);

        $test = $repository->find('fake-schedule-id', 'fake-id-001');

        $this->assertNotNull($test);
        $this->assertEquals('fake-id-001', $test->getId());
        $this->assertEquals('fake-schedule-id', $test->getScheduleId());
        $this->assertEquals('fake-name', $test->getName());
        $this->assertEquals('2016-04-09 18:19:20', $test->getStart()->format('Y-m-d H:i:s'));
        $this->assertEquals('2016-05-09 17:18:19', $test->getEnd()->format('Y-m-d H:i:s'));
        $this->assertEquals('fake category', $test->getCategory());
        $this->assertEquals(2, count($test->getTags()));
        $this->assertEquals('a', $test->getTags()[0]);
        $this->assertEquals('b', $test->getTags()[1]);
        $this->assertEquals('fake room', $test->getRoom());
        $this->assertEquals(2, count($test->getHosts()));
        $this->assertEquals('jane', $test->getHosts()[0]);
        $this->assertEquals('john', $test->getHosts()[1]);
        $this->assertEquals('fake description', $test->getDescription());
        $this->assertEquals('http://nope.city/.gif', $test->getBanner());
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

        $repository = new CassandraEventRepository($stubClient);

        $repository->find('no-schedule', 'missing-id');
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

                $hosts = new Collection(Cassandra::TYPE_VARCHAR);
                $hosts->add('jane');
                $hosts->add('john');

                return new FakeCassandraRows([
                    [
                        'id' => 'fake-id-001',
                        'schedule_id' => 'fake-schedule-id',
                        'name' => 'fake-name',
                        'start' =>  new Cassandra\Timestamp(1460225960),
                        'end' =>  new Cassandra\Timestamp(1462814299),
                        'category' => 'fake category',
                        'tags' => $tags,
                        'room' => 'fake room',
                        'hosts' => $hosts,
                        'description' => 'fake description',
                        'banner' => 'http://nope.city/.gif',
                    ],
                    [
                        'id' => 'fake-id-002',
                        'schedule_id' => 'fake-schedule-id',
                    ]
                ]);
            }
        };
        $repository = new CassandraEventRepository($stubClient);

        $results = $repository->findAll('fake-schedule-id');

        $this->assertEquals(2, count($results));
        $test = $results[0];
        $test2 = $results[1];
        $this->assertNotNull($test);
        $this->assertNotNull($test2);
        $this->assertEquals('fake-id-001', $test->getId());
        $this->assertEquals('fake-id-002', $test2->getId());;
        $this->assertEquals('fake-schedule-id', $test->getScheduleId());
        $this->assertEquals('fake-schedule-id', $test2->getScheduleId());
        $this->assertEquals('fake-name', $test->getName());
        $this->assertEquals('2016-04-09 18:19:20', $test->getStart()->format('Y-m-d H:i:s'));
        $this->assertEquals('2016-05-09 17:18:19', $test->getEnd()->format('Y-m-d H:i:s'));
        $this->assertEquals('fake category', $test->getCategory());
        $this->assertEquals(2, count($test->getTags()));
        $this->assertEquals('a', $test->getTags()[0]);
        $this->assertEquals('b', $test->getTags()[1]);
        $this->assertEquals('fake room', $test->getRoom());
        $this->assertEquals(2, count($test->getHosts()));
        $this->assertEquals('jane', $test->getHosts()[0]);
        $this->assertEquals('john', $test->getHosts()[1]);
        $this->assertEquals('fake description', $test->getDescription());
        $this->assertEquals('http://nope.city/.gif', $test->getBanner());
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
        $repository = new CassandraEventRepository($stubClient);

        $results = $repository->findAll('schedule-id');

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
                $this->test->assertEquals('fake-event-id-001', $arguments['id']);
                $this->test->assertEquals('fake-id-001', $arguments['schedule_id']);
                $this->test->assertEquals('Fake Event', $arguments['name']);
                $this->test->assertEquals(671199300 , $arguments['start']->getTimestamp());
                $this->test->assertEquals(671282100, $arguments['end']->getTimestamp());
                $this->test->assertEquals('category', $arguments['category']);
                $this->test->assertInstanceOf(Collection::class, $arguments['tags']);
                $this->test->assertEquals('tag a', $arguments['tags']->get(0));
                $this->test->assertEquals('tag b', $arguments['tags']->get(1));
                $this->test->assertEquals('fake room', $arguments['room']);
                $this->test->assertInstanceOf(Collection::class, $arguments['hosts']);
                $this->test->assertEquals('John Doe', $arguments['hosts']->get(0));
                $this->test->assertEquals('Jane Doe', $arguments['hosts']->get(1));
                $this->test->assertEquals('This is a fake event description', $arguments['description']);
                $this->test->assertEquals('http://google.com/google.png', $arguments['banner']);

                return new FakeCassandraRows([]);
            }
        };
        $repository = new CassandraEventRepository($spyClient);

        $data = new Event(
            'fake-event-id-001',
            'fake-id-001',
            'Fake Event',
            new DateTime("1991-04-09 07:15:00-5:00"),
            new DateTime("1991-04-10 06:15:00-5:00"),
            'category',
            ['tag a', 'tag b'],
            'fake room',
            ['John Doe', 'Jane Doe'],
            'This is a fake event description',
            'http://google.com/google.png'
        );

        $result = $repository->save($data);

        $this->assertEquals('fake-event-id-001', $result);
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
                $this->test->assertNull($arguments['schedule_id']);
                $this->test->assertNull($arguments['name']);
                $this->test->assertNull($arguments['start']);
                $this->test->assertNull($arguments['end']);
                $this->test->assertNull($arguments['category']);
                $this->test->assertInstanceOf(Collection::class, $arguments['tags']);
                $this->test->assertEquals(0, $arguments['tags']->count());
                $this->test->assertNull($arguments['room']);
                $this->test->assertInstanceOf(Collection::class, $arguments['hosts']);
                $this->test->assertEquals(0, $arguments['hosts']->count());
                $this->test->assertNull($arguments['description']);
                $this->test->assertNull($arguments['banner']);

                return new FakeCassandraRows([]);
            }
        };
        $repository = new CassandraEventRepository($spyClient);

        $data = new Event();

        $result = $repository->save($data);

        $this->assertNotNull($result);
    }
}
