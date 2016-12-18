<?php

namespace Tapalava\ScheduleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use JMS\DiExtraBundle\Annotation\Inject;
use JMS\DiExtraBundle\Annotation\InjectParams;
use Tapalava\Schedule\Schedule;
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
     * @InjectParams({
     *     "scheduleRepository" = @Inject("schedule.repository")
     * })
     */
    public function __construct(ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    /**
     * Create a new schedule page
     *
     * @Template
     * @Route("/create.{_format}", methods={"GET"}, name="schedule-create", defaults={"_format" = "html"})
     */
    public function createAction()
    {
        return [];
    }

    /**
     * Create a new schedule page
     *
     * @Template
     * @Route("/create.{_format}", methods={"POST"}, name="schedule-create-submit", defaults={"_format" = "html"})
     */
    public function createSubmitAction($_format)
    {
        return $this->redirectToRoute(
            'schedule-read',
            ['id' => 'fake-id-001', '_format' => $_format]
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
