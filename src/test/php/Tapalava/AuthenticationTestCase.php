<?php

namespace Tapalava;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

abstract class AuthenticationTestCase extends WebTestCase
{
    /**
     * Create a logged in client for testing authentication.
     */
    protected function logIn(array $roles): Client
    {
        $client = static::createClient();
        $session = $client->getContainer()->get('session');

        $firewall = 'main';
        $token = new UsernamePasswordToken('FakeTestUser', null, $firewall, $roles);
        $session->set('_security_' . $firewall, serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);

        return $client;
    }

    /**
     * Asserts that a response object is a redirect response to the site login page.
     *
     * @param Response $response
     */
    protected function assertLoginResponse(Response $response)
    {
        $this->assertRedirect('/login', $response);
    }

    /**
     * Asserts that a redirect response is pointing to a specific path.
     *
     * @param string $expectedPath The path the response should be pointed to.
     * @param Response $actual The actual response object received.
     */
    protected function assertRedirect($expectedPath, Response $actual)
    {
        $this->assertEquals(302, $actual->getStatusCode());
        $actualPath = parse_url($actual->headers->get('Location'))['path'];
        $this->assertEquals($expectedPath, $actualPath);
    }
}
