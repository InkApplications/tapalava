<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EventControllerTest extends WebTestCase
{
    /**
     * Events's index page should be returning successful.
     *
     * @test
     * @group functional
     */
    public function scheduleRead()
    {
        $client = static::createClient();

        $client->request('GET', '/schedule/fake-id-001/events');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/schedule/fake-id-001/events.json');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Event's index page should 404 when the ID doesn't exist.
     *
     * @test
     * @group functional
     */
    public function scheduleReadMissing()
    {
        $client = static::createClient();

        $client->request('GET', '/schedule/missing-id/events');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Form page for creating a new event should load successfully.
     *
     * @test
     * @group functional
     */
    public function createEventForm()
    {
        $client = static::createClient();

        $client->request('GET', '/schedule/fake-id-001/create');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Form page should 404 if the schedule ID isn't found.
     *
     * @test
     * @group functional
     */
    public function createEventFormMissing()
    {
        $client = static::createClient();

        $client->request('GET', '/schedule/missing-id/create');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }

    /**
     * Form page for creating a new event should load successfully.
     *
     * @test
     * @group functional
     */
    public function createEventFormSubmit()
    {
        $client = static::createClient();

        $client->request('POST', '/schedule/fake-id-001/create.json', [], [], [], '{"event": {}}');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
