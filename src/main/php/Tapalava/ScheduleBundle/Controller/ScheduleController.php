<?php

namespace Tapalava\ScheduleBundle\Controller;

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

    /**
     * @InjectParams({
     *     "scheduleRepository" = @Inject("schedule.repository"),
     *     "formTransformer" = @Inject("schedule.form_transformer"),
     *     "requestParser" = @Inject("http.request_parser"),
     * })
     */
    public function __construct(
        ScheduleRepository $scheduleRepository,
        ScheduleFormTransformer $formTransformer,
        RequestParser $requestParser
    ) {
        $this->scheduleRepository = $scheduleRepository;
        $this->formTransformer = $formTransformer;
        $this->requestParser = $requestParser;
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

        return $this->redirectToRoute(
            'schedule-read',
            ['id' => $id, '_format' => $_format]
        );
    }

    /**
     * Read the Information about a schedule.
     *
     * @Template
     * @Route("/{id}.{_format}", methods={"GET"}, name="schedule-read", defaults={"_format" = "html"})
     * @param string $id The UUID of the schedule to display information about.
     * @return array templating information
     */
    public function readAction($id)
    {
        try {
            return [
                'schedule' => $this->scheduleRepository->find($id),
            ];
        } catch (ScheduleNotFoundException $e) {
            throw new NotFoundHttpException("Schedule was not found");
        }
    }
}
