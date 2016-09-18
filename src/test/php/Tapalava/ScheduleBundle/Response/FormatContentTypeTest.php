<?php

namespace Tapalava\ScheduleBundle\Response;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

use PHPUnit_Framework_TestCase as TestCase;

class FormatContentTypeTest extends TestCase
{
    /**
     * Each request format should get its appropriate response type back.
     *
     * @test
     */
    public function contentTypeFormat()
    {
        $test = new FormatContentType();
        $event = $this->getJsonRequestEvent();

        $test->onResponse($event);

        $this->assertEquals('application/json', $event->getResponse()->headers->get('Content-Type'));
    }

    /**
     * A regular request should not be modified.
     *
     * @test
     */
    public function defaultType()
    {
        $test = new FormatContentType();
        $event = $this->getGenericRequestEvent();

        $test->onResponse($event);

        $this->assertNull($event->getResponse()->headers->get('Content-Type'));
    }

    private function getJsonRequestEvent() : FilterResponseEvent
    {
        return new class extends FilterResponseEvent {
            private $response;
            public function __construct() {
                $this->response = new Response();
            }
            public function getResponse() {
                return $this->response;
            }
            public function getRequest() {
                $request = new Request();
                $request->setRequestFormat('json');
                return $request;
            }
        };
    }

    private function getGenericRequestEvent() : FilterResponseEvent
    {
        return new class extends FilterResponseEvent {
            private $response;
            public function __construct() {
                $this->response = new Response();
            }
            public function getResponse() {
                return $this->response;
            }
            public function getRequest() {
                return new Request();
            }
        };
    }
}
