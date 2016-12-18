<?php

namespace Tapalava\User;

use DateTime;
use PHPUnit_Framework_TestCase as TestCase;

class FakeUserRepositoryTest extends TestCase
{
    /**
     * Ensure there are no errors when saving a user.
     *
     * @test
     */
    public function saveCredentials()
    {
        $test = new FakeUserRepository();

        $test->saveUserCredentials(new User());
    }

    /**
     * Make sure we get a fake user back when looking one up.
     *
     * @test
     */
    public function findCredentialsByEmail()
    {
        $test = new FakeUserRepository();

        $result = $test->findCredentialsByEmail('doesnt-matter');

        $this->assertEquals('johndoe@tapalava.com', $result->getUsername());
        $this->assertEquals('$2y$13$.seDcm.uabVc6HvQshpp7.9fQJrChYuid6zRvZD0BXtegOPV0Aja2', $result->getPassword());
        $this->assertEquals('def-456', $result->getSalt());
        $this->assertInstanceOf(DateTime::class, $result->getPasswordCreated());
    }

    /**
     * Ensure we can create a fake user object.
     *
     * @test
     */
    public function createUser()
    {
        $test = new FakeUserRepository();

        $result = $test->createUser('test-email');

        $this->assertEquals('test-email', $result->getUsername());
    }
}
