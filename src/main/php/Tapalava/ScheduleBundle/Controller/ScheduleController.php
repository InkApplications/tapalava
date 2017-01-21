<?php

namespace Tapalava\ScheduleBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Tapalava\Http\RequestParser;
use Tapalava\Schedule\Schedule;
use Tapalava\Schedule\ScheduleFormTransformer;
use Tapalava\Schedule\ScheduleNotFoundException;
use Tapalava\Schedule\ScheduleRepository;

/**
 * Actions related to a specific schedule
 *
 * @Route("/schedule")
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class ScheduleController extends Controller
{
    /**
     * @var ScheduleRepository For accessing schedule data.
     */
    private $scheduleRepository;

    /**
     * @var ScheduleFormTransformer for converting to/from view and form data.
     */
    private $formTransformer;

    /** @var RequestParser Used for extracting view data from HTTP Reqeusts. */
    private $requestParser;

    /** @var LoggerInterface application log. */
    private $logger;

    /**
     * @InjectParams({
     *     "scheduleRepository" = @Inject("schedule.repository"),
     *     "formTransformer" = @Inject("schedule.form_transformer"),
     *     "requestParser" = @Inject("http.request_parser"),
     *     "logger" = @Inject("logger")
     * })
     */
    public function __construct(
        ScheduleRepository $scheduleRepository,
        ScheduleFormTransformer $formTransformer,
        RequestParser $requestParser,
        LoggerInterface $logger
    ) {
        $this->scheduleRepository = $scheduleRepository;
        $this->formTransformer = $formTransformer;
        $this->requestParser = $requestParser;
        $this->logger = $logger;
    }

    /**
     * Editor page for creating a new schedule entity.
     *
     * @Template
     * @Route("/create.{_format}", methods={"GET"}, name="schedule-create", defaults={"_format" = "html"})
     * @Security("has_role('ROLE_USER')")
     */
    public function createAction()
    {
        return [];
    }

    /**
     * Receive a request for creating a new schedule entity.
     *
     * @Template
     * @Route("/create.{_format}", methods={"POST"}, name="schedule-create-submit", defaults={"_format" = "html"})
     * @Security("has_role('ROLE_USER')")
     */
    public function createSubmitAction($_format, Request $request)
    {
        $data = $this->requestParser->getEntityFromPost($request, 'schedule');
        $schedule = $this->formTransformer->fromView($data);
        $id = $this->scheduleRepository->save($schedule);
        $this->logger->info("Created Schedule with ID: $id");

        return $this->redirectToRoute(
            'schedule-read',
            ['schedule' => $id, '_format' => $_format]
        );
    }

    /**
     * Read the Information about a schedule.
     *
     * @Template
     * @Route("/{schedule}.{_format}", methods={"GET"}, name="schedule-read", defaults={"_format" = "html"})
     * @param Schedule $schedule The Schedule to be displayed.
     * @return array templating information
     */
    public function readAction(Schedule $schedule)
    {
        return [
            'schedule' => $schedule,
        ];
    }
}
