<?php

namespace Tapalava\Event;

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

        $test = $repository->find('fake-event-id-001');

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

        $repository->find('missing-id');
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
