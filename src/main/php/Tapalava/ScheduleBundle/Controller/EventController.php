<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Tapalava\Event\EventNotFoundException;
use Tapalava\Event\EventRepository;
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

    private $eventRepository;

    /**
     * @InjectParams({
     *     "scheduleRepository" = @Inject("schedule.repository"),
     *     "eventRepository" = @Inject("event.repository")
     * })
     */
    public function __construct(ScheduleRepository $scheduleRepository, EventRepository $eventRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
        $this->eventRepository = $eventRepository;
    }

    /**
     * List all of the events for a schedule.
     *
     * @Route("/events.{_format}", methods={"GET"}, name="events-read", defaults={"_format" = "html"})
     * @Template
     */
    public function indexAction($schedule)
    {
        try {
            return [
                'schedule' => $this->scheduleRepository->find($schedule),
                'events' => $this->eventRepository->findAll($schedule),
            ];
        } catch (ScheduleNotFoundException $e) {
            throw new NotFoundHttpException("Schedule was not found");
        }
    }
}
