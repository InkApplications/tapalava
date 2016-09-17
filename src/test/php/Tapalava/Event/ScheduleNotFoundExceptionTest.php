<?php

namespace Tapalava\Event;

use PHPUnit_Framework_TestCase as TestCase;
use Exception;

class EventNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function attributes()
    {
        $test = new EventNotFoundException('id-001', new Exception('test'));

        $this->assertEquals('id-001', $test->getCriteria());
        $this->assertEquals('Could not find Event', $test->getMessage());
        $this->assertEquals(EventNotFoundException::CODE, $test->getCode());
        $this->assertNotNull($test->getPrevious());
        $this->assertEquals('test', $test->getPrevious()->getMessage());
    }
}
