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

        $client->request('GET', '/schedule/fake-id-001/events');
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

        $client->request('GET', '/schedule/missing-id');
        $this->assertEquals(404, $client->getResponse()->getStatusCode());
    }
}
