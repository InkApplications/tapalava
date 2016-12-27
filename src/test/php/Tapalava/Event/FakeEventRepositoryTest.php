<?php

namespace Tapalava\Event;

use DateTime;
use PHPUnit_Framework_TestCase as TestCase;

class FakeEventRepositoryTest extends TestCase
{
    /**
     * Single events can be looked up by valid ID
     *
     * @test
     */
    public function find()
    {
        $repository = new FakeEventRepository();

        $test = $repository->find('fake-id-001', 'fake-event-id-001');

        $this->assertNotNull($test);
    }

    /**
     * A missing ID should throw an exception
     *
     * @test
     * @expectedException \Tapalava\Event\EventNotFoundException
     */
    public function notFound()
    {
        $repository = new FakeEventRepository();

        $repository->find('fake-id-001', 'missing-id');
    }

    /**
     * Group of events can be looked up by valid Schedule ID.
     *
     * @test
     */
    public function findCollection()
    {
        $repository = new FakeEventRepository();

        $test = $repository->findAll('fake-id-001');

        $this->assertNotEmpty($test);
    }

    /**
     * Entities can be saved without error
     *
     * @test
     */
    public function save()
    {
        $repository = new FakeEventRepository();

        $test = $repository->save(new Event('fake-id', 'fake-schedule-id', 'fake name', new DateTime(), new DateTime(), 'fake category', ['a', 'b'], 'fake room', ['john'], 'fake description', 'fake banner'));
        $testEmpty = $repository->save(new Event());

        $this->assertNotEmpty($test);
        $this->assertNotEmpty($testEmpty);
    }

    /**
     * Exception is thrown when an invalid schedule ID is provided when
     * looking up a collection of events.
     *
     * @test
     * @expectedException \Tapalava\Schedule\ScheduleNotFoundException
     */
    public function collectionNotFound()
    {
        $repository = new FakeEventRepository();

        $repository->findAll('missing-id');
    }
}
