<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Tapalava\Schedule\ScheduleNotFoundException;
use Tapalava\Schedule\ScheduleRepository;

/**
 * Actions surrounding a specific schedule's events.
 *
 * @Route("/schedule/{schedule}")
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class EventController
{
    /**
     * @var ScheduleRepository For accessing schedule data.
     */
    private $scheduleRepository;

    /**
     * @InjectParams({
     *     "scheduleRepository" = @Inject("schedule.repository")
     * })
     */
    public function __construct(ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * List all of the events for a schedule.
     *
     * @Route("/events", methods={"GET"}, name="events-read")
     * @Template
     */
    public function indexAction($schedule)
    {
        try {
            return [
                'schedule' => $this->scheduleRepository->find($schedule),
                'events' => [],
            ];
        } catch (ScheduleNotFoundException $e) {
            throw new NotFoundHttpException("Schedule was not found");
        }
    }
}
