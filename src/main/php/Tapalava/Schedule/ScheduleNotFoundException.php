<?php

namespace Tapalava\Schedule;

use Exception;

/**
 * Indicates that a Schedule was requested by some criteria, but was not
 * able to be located by the system.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class ScheduleNotFoundException extends Exception
{
    const CODE = 749414174000201;

    /**
     * @var string The criteria used to attempt to locate the schedule.
     */
    private $criteria;

    /**
     * @param string $criteria The criteria used to attempt to locate the schedule.
     * @param Exception|null $previous A previous exception (optional)
     */
    public function __construct(string $criteria, Exception $previous = null)
    {
        parent::__construct("Could not find Schedule", self::CODE, $previous);

        $this->criteria = $criteria;
    }

    /**
     * @return string The criteria used to attempt to locate the schedule.
     */
    public function getCriteria()
    {
        return $this->criteria;
    }
}
