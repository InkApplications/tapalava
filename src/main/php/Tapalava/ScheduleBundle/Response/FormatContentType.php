<?php

namespace Tapalava\ScheduleBundle\Response;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Adds content-types to Responses based on the request Format.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class FormatContentType
{
    /**
     * Event Listener for a Kernel Response.
     *
     * @param FilterResponseEvent $event Kernel Response event.
     */
    public function onResponse(FilterResponseEvent $event)
    {
        $format = $event->getRequest()->getRequestFormat();
        $response = $event->getResponse();

        switch ($format) {
            case 'json':
                $response->headers->set('Content-Type', 'application/json', true);
        }
    }
}
