<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Tapalava\Event\EventFormTransformer;
use Tapalava\Event\EventRepository;
use Tapalava\Http\RequestParser;
use Tapalava\Schedule\Schedule;
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
    public function indexAction(Schedule $schedule)
    {
        try {
            return [
                'schedule' => $schedule,
                'events' => $this->eventRepository->findAll($schedule->getId()),
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
     * @Security("has_role('ROLE_USER') and is_granted('create_event', schedule)")
     */
    public function createAction(Schedule $schedule)
    {
        return [
            'schedule' => $schedule,
        ];
    }

    /**
     * Form page for creating a new event for a schedule.
     *
     * @Route("/create.{_format}", methods={"POST"}, name="event-create-submit", defaults={"_format" = "html"})
     * @Security("has_role('ROLE_USER') and is_granted('create_event', schedule)")
     */
    public function createSubmitAction($_format, Request $request, Schedule $schedule)
    {
        $data = $this->requestParser->getEntityFromPost($request, 'event');
        $event = $this->formTransformer->fromView($data, $schedule->getId());
        $id = $this->eventRepository->save($event);

        return $this->redirectToRoute(
            'events-read',
            ['schedule' => $schedule->getId(), '_format' => $_format]
        );
    }
}
