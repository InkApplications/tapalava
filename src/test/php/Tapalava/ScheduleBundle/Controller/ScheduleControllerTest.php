<?php

namespace Tapalava\ScheduleBundle\Controller;

use Tapalava\AuthenticationTestCase;

class ScheduleControllerTest extends AuthenticationTestCase
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

        $client->request('GET', '/schedule/fake-id-001.html');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/schedule/fake-id-002.html');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('GET', '/schedule/fake-id-001.json');
        $decoded = json_decode($client->getResponse()->getContent(), true);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertNotFalse($decoded, 'invalid json document returned');
        $this->assertTrue(is_array($decoded), 'invalid json document returned');

        $client->request('GET', '/schedule/fake-id-002.json');
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

    /**
     * Schedule's create page should return successfully
     *
     * @test
     * @group functional
     */
    public function scheduleCreateLoggedOut()
    {
        $client = static::createClient();

        $client->request('GET', '/schedule/create.html');
        $this->assertLoginResponse($client->getResponse());
    }

    /**
     * Schedule's create page should return successfully
     *
     * @test
     * @group functional
     */
    public function scheduleCreate()
    {
        $client = $this->logIn(['ROLE_USER']);

        $client->request('GET', '/schedule/create.html');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /**
     * Creating a schedule should result in a redirect to the created schedule.
     *
     * @test
     * @group functional
     */
    public function scheduleCreateSubmitLoggedOut()
    {
        $client = static::createClient();

        $client->request('POST', '/schedule/create.json', [], [], [], '{"schedule": {}}');
        $this->assertLoginResponse($client->getResponse());
    }

    /**
     * Creating a schedule should result in a redirect to the created schedule.
     *
     * @test
     * @group functional
     */
    public function scheduleCreateSubmit()
    {
        $client = $this->logIn(['ROLE_USER']);

        $client->request('POST', '/schedule/create.json', [], [], [], '{"schedule": {}}');
        $this->assertRedirect('/schedule/fake-generated-id.json', $client->getResponse());
    }
}
