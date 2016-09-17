<?php

namespace Tapalava\Event;

use PHPUnit_Framework_TestCase as TestCase;
use DateTime;

class EventTest extends TestCase
{
    /**
     * Check that each of the attributes works as expected.
     *
     * @test
     */
    public function attributes()
    {
        $test = new Event(
            'fake-event-id-001',
            'fake-id-001',
            'fake name',
            new DateTime('2017-05-14T16:00:00-5:00'),
            new DateTime('2017-05-14T17:00:00-5:00'),
            'Fake Category',
            ['tag-a', 'tag-b'],
            'fake room',
            ['john doe', 'jane doe'],
            'fake description',
            'fake scrim uri'
        );

        $this->assertEquals('fake-event-id-001', $test->getId());
        $this->assertEquals('fake-id-001', $test->getScheduleId());
        $this->assertEquals('fake name', $test->getName());
        $this->assertEquals(new DateTime('2017-05-14T16:00:00-5:00'), $test->getStart());
        $this->assertEquals(new DateTime('2017-05-14T17:00:00-5:00'), $test->getEnd());
        $this->assertEquals('Fake Category', $test->getCategory());
        $this->assertEquals('fake room', $test->getRoom());
        $this->assertEquals(2, count($test->getHosts()));
        $this->assertEquals('john doe', $test->getHosts()[0]);
        $this->assertEquals('jane doe', $test->getHosts()[1]);
        $this->assertEquals(2, count($test->getTags()));
        $this->assertEquals('tag-a', $test->getTags()[0]);
        $this->assertEquals('tag-b', $test->getTags()[1]);
        $this->assertEquals('fake description', $test->getDescription());
        $this->assertEquals('fake scrim uri', $test->getScrim());
    }

    /**
     * Check that there are reasonable defaults to each of the attributes.
     *
     * @test
     */
    public function defaults()
    {
        $test = new Event();

        $this->assertNull($test->getId());
        $this->assertNull($test->getScheduleId());
        $this->assertNull($test->getName());
        $this->assertNull($test->getStart());
        $this->assertNull($test->getEnd());
        $this->assertNull($test->getCategory());
        $this->assertNull($test->getDescription());
        $this->assertNull($test->getScrim());
        $this->assertNull($test->getRoom());

        $this->assertNotNull($test->getTags());
        $this->assertEquals(0, count($test->getTags()));

        $this->assertNotNull($test->getHosts());
        $this->assertEquals(0, count($test->getHosts()));
    }
}
