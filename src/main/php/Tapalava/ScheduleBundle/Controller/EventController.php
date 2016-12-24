<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Tapalava\Event\EventFormTransformer;
use Tapalava\Event\EventRepository;
use Tapalava\Http\RequestParser;
use Tapalava\Schedule\ScheduleNotFoundException;
use Tapalava\Schedule\ScheduleRepository;

/**
 * Actions surrounding a specific schedule's events.
 *
 * @Route("/schedule/{schedule}")
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class EventController extends Controller
{
    /** @var ScheduleRepository For accessing schedule data. */
    private $scheduleRepository;

    /** @var EventRepository Service for looking up data about a schedule's events. */
    private $eventRepository;

    /** @var RequestParser For transforming form posts into a consistent format. */
    private $requestParser;

    /** @var EventFormTransformer For transforming data to and from a view format. */
    private $formTransformer;

    /**
     * @InjectParams({
     *     "scheduleRepository" = @Inject("schedule.repository"),
     *     "eventRepository" = @Inject("event.repository"),
     *     "requestParser" = @Inject("http.request_parser"),
     *     "formTransformer" = @Inject("event.form_transformer")
     * })
     */
    public function __construct(
        ScheduleRepository $scheduleRepository,
        EventRepository $eventRepository,
        RequestParser $requestParser,
        EventFormTransformer $formTransformer
    ) {
        $this->scheduleRepository = $scheduleRepository;
        $this->eventRepository = $eventRepository;
        $this->requestParser = $requestParser;
        $this->formTransformer = $formTransformer;
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

    /**
     * Form page for creating a new event for a schedule.
     *
     * @Route("/create", methods={"GET"}, name="event-create")
     * @Template
     */
    public function createAction($schedule)
    {
        try {
            return [
                'schedule' => $this->scheduleRepository->find($schedule),
            ];
        } catch (ScheduleNotFoundException $e) {
            throw new NotFoundHttpException("Schedule was not found");
        }
    }

    /**
     * Form page for creating a new event for a schedule.
     *
     * @Route("/create.{_format}", methods={"POST"}, name="event-create-submit", defaults={"_format" = "html"})
     */
    public function createSubmitAction($_format, Request $request, $schedule)
    {
        $data = $this->requestParser->getEntityFromPost($request, 'event');
        $event = $this->formTransformer->fromView($data, $schedule);
        $id = $this->eventRepository->save($event);

        return $this->redirectToRoute(
            'events-read',
            ['schedule' => $schedule, '_format' => $_format]
        );
    }
}
