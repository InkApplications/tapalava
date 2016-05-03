<?php

namespace Tapalava\Schedule;

use PHPUnit_Framework_TestCase as TestCase;

class FakeScheduleRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function find()
    {
        $repository = new FakeScheduleRepository();

        $test = $repository->find('fake-id-001');

        $this->assertNotNull($test);
    }

    /**
     * @test
     * @expectedException \Tapalava\Schedule\ScheduleNotFoundException
     */
    public function notFound()
    {
        $repository = new FakeScheduleRepository();

        $repository->find('missing-id');
    }
}
