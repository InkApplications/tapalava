<?php

namespace Tapalava\ScheduleBundle\ParamConverter;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tapalava\Schedule\Schedule;
use Tapalava\Schedule\ScheduleNotFoundException;
use Tapalava\Schedule\ScheduleRepository;

/**
 * Loads Schedules from the schedule Repository based on the ID requested.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class ScheduleParamConverter implements ParamConverterInterface
{
    /** @var ScheduleRepository */
    private $scheduleRepository;

    /**
     * @param ScheduleRepository $scheduleRepository
     */
    public function __construct(ScheduleRepository $scheduleRepository)
    {
        $this->scheduleRepository = $scheduleRepository;
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();
        $param = $configuration->getName();

        try {
            $object = $this->scheduleRepository->find($request->get($param));
        } catch (ScheduleNotFoundException $notFoundException) {
            $object = null; // Null case handled below.
        }

        if (null === $object && false === $configuration->isOptional()) {
            throw new NotFoundHttpException(sprintf('%s object not found.', $class));
        }

        $request->attributes->set($configuration->getName(), $object);
    }

    public function supports(ParamConverter $configuration)
    {
        $class = $configuration->getClass();

        if (null === $class) {
            return false;
        }

        if (Schedule::class === $class) {
            return true;
        }
    }
}
