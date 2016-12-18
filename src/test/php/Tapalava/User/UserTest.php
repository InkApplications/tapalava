<?php

namespace Tapalava\User;

use DateTime;
use PHPUnit_Framework_TestCase as TestCase;

class UserTest extends TestCase
{
    /**
     * Check that each of the user attributes works as expected.
     *
     * @test
     */
    public function attributes()
    {
        $test = new User(
            'test-id',
            'tester@tapalava.com',
            ['role-a', 'role-b'],
            'test-password',
            'test-salt',
            new DateTime('2016-01-02T3:45:57Z')
        );

        $this->assertEquals('test-id', $test->getId());
        $this->assertEquals('tester@tapalava.com', $test->getUsername());
        $this->assertEquals('tester@tapalava.com', $test->getEmail());
        $this->assertEquals(2, count($test->getRoles()));
        $this->assertEquals('role-a', $test->getRoles()[0]);
        $this->assertEquals('role-b', $test->getRoles()[1]);
        $this->assertEquals('test-password', $test->getPassword());
        $this->assertEquals('test-salt', $test->getSalt());
        $this->assertEquals(new DateTime('2016-01-02T3:45:57Z'), $test->getPasswordCreated());
    }

    /**
     * Check that there are reasonable defaults to each of the user attributes.
     *
     * @test
     */
    public function defaults()
    {
        $test = new User();

        $this->assertNull($test->getId());
        $this->assertNull($test->getUsername());
        $this->assertNull($test->getEmail());
        $this->assertNull($test->getPassword());
        $this->assertNull($test->getSalt());
        $this->assertNull($test->getPasswordCreated());
        $this->assertNotNull($test->getRoles());
        $this->assertEquals(0, count($test->getRoles()));
    }
}
