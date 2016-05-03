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
 * Actions related to a specific schedule
 *
 * @Route("/schedule")
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class ScheduleController
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
     * Read the Information about a schedule.
     *
     * @Template
     * @Route("/{id}", methods={"GET"}, name="schedule-read")
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
