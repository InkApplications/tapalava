<?php

namespace Tapalava\Schedule;

use PHPUnit_Framework_TestCase as TestCase;
use DateTime;

class ScheduleTest extends TestCase
{
    /**
     * Check that each of the attributes works as expected.
     *
     * @test
     */
    public function attributes()
    {
        $test = new Schedule(
            'fake-id-001',
            'fake name',
            [new DateTime('1991-04-09'), new DateTime('2017-05-14')],
            'fake description',
            'fake banner uri',
            'a fake location',
            ['tag a', 'tag b']
        );

        $this->assertEquals('fake-id-001', $test->getId());
        $this->assertEquals('fake name', $test->getName());
        $this->assertEquals(2, count($test->getDays()));
        $this->assertEquals(new DateTime('1991-04-09'), $test->getDays()[0]);
        $this->assertEquals(new DateTime('2017-05-14'), $test->getDays()[1]);
        $this->assertEquals('fake description', $test->getDescription());
        $this->assertEquals('fake banner uri', $test->getBanner());
        $this->assertEquals('a fake location', $test->getLocation());
        $this->assertEquals(2, count($test->getTags()));
        $this->assertEquals('tag a', $test->getTags()[0]);
        $this->assertEquals('tag b', $test->getTags()[1]);
    }

    /**
     * Check that there are reasonable defaults to each of the attributes.
     *
     * @test
     */
    public function defaults()
    {
        $test = new Schedule();

        $this->assertNull($test->getId());
        $this->assertNull($test->getName());

        $this->assertNotNull($test->getDays());
        $this->assertEquals(0, count($test->getDays()));

        $this->assertNull($test->getDescription());
        $this->assertNull($test->getBanner());
        $this->assertNull($test->getLocation());

        $this->assertNotNull($test->getTags());
        $this->assertEquals(0, count($test->getTags()));
    }
}
