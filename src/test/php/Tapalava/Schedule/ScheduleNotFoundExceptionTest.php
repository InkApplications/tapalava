<?php

namespace Tapalava\Schedule;

use PHPUnit_Framework_TestCase as TestCase;
use Exception;

class ScheduleNotFoundExceptionTest extends TestCase
{
    /**
     * @test
     */
    public function attributes()
    {
        $test = new ScheduleNotFoundException('id-001', new Exception('test'));

        $this->assertEquals('id-001', $test->getCriteria());
        $this->assertEquals('Could not find Schedule', $test->getMessage());
        $this->assertEquals(ScheduleNotFoundException::CODE, $test->getCode());
        $this->assertNotNull($test->getPrevious());
        $this->assertEquals('test', $test->getPrevious()->getMessage());
    }
}
