<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ScheduleControllerTest extends WebTestCase
{
    /**
     * Schedule's read page should be returning successful.
     *
     * @test
     * @group functional
     */
    public function scheduleRead()
    {
        $client = static::createClient();

        $client->request('GET', '/schedule/fake-id-001');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/schedule/fake-id-002');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Schedule read page should 404 when the ID doesn't exist.
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
