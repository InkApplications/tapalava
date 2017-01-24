<?php

namespace Tapalava\User;

use PHPUnit_Framework_TestCase as TestCase;

class ProfileTest extends TestCase
{
    /**
     * Check that each of the user attributes works as expected.
     *
     * @test
     */
    public function attributes()
    {
        $test = new Profile(
            'test-id',
            ['tester@tapalava.com', 'tester2@tapalava.com'],
            'test name'
        );

        $this->assertEquals('test-id', $test->getId());
        $this->assertEquals('test name', $test->getName());
        $this->assertEquals(2, count($test->getEmails()));
        $this->assertEquals('tester@tapalava.com', $test->getEmails()[0]);
        $this->assertEquals('tester2@tapalava.com', $test->getEmails()[1]);
    }

    /**
     * Check that there are reasonable defaults to each of the user attributes.
     *
     * @test
     */
    public function defaults()
    {
        $test = new Profile();

        $this->assertNull($test->getId());
        $this->assertNull($test->getName());
        $this->assertNotNull($test->getEmails());
        $this->assertEquals(0, count($test->getEmails()));
    }
}
