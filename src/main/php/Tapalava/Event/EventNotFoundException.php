<?php

namespace Tapalava\Event;

use Exception;

/**
 * Thrown if an Event can not be found when requested.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class EventNotFoundException extends Exception
{
    const CODE = 749414174000202;

    /**
     * @var string The criteria used to attempt to locate the schedule.
     */
    private $criteria;

    /**
     * @param string $criteria The criteria used to attempt to locate the event.
     * @param Exception|null $previous A previous exception (optional)
     */
    public function __construct(string $criteria, Exception $previous = null)
    {
        parent::__construct("Could not find Event", self::CODE, $previous);

        $this->criteria = $criteria;
    }

    /**
     * @return string The criteria used to attempt to locate the event.
     */
    public function getCriteria()
    {
        return $this->criteria;
    }
}
