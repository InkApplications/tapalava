<?php

namespace Tapalava\Schedule;

/**
 * Represents a grouping of many events for one larger-event.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class Schedule
{
    /**
     * @var string A UUID For this object.
     */
    private $id;

    /**
     * @var string The name of the large-event that this schedule is for.
     */
    private $name;

    /**
     * @var string A User provided description of what the large-event is. (optional)
     */
    private $description;

    /**
     * @var string A URI to an image that can be used to represent this event. (optional)
     */
    private $banner;

    /**
     * @var array<DateTime> a list of dates that this event occurs on.
     */
    private $days = [];

    /**
     * @var string An address for the location, if applicable. (optional)
     */
    private $location;

    /**
     * @var array<string> A list of meta-data descriptions for the schedule.
     */
    private $tags = [];

    /**
     * @param string|null $id A UUID For this object.
     * @param string|null $name The name of the large-event that this schedule is for.
     * @param string|null $description A User provided description of what the large-event is. (optional)
     * @param string|null $banner A URI to an image that can be used to represent this event. (optional)
     * @param array $days A list of dates that this event occurs on.
     * @param string|null $location An address for the location, if applicable. (optional)
     * @param array $tags A list of meta-data descriptions for the schedule.
     */
    public function __construct(
        $id = null,
        $name = null,
        array $days = null,
        $description = null,
        $banner = null,
        $location = null,
        array $tags = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->banner = $banner;
        $this->days = $days ?: [];
        $this->location = $location;
        $this->tags = $tags ?: [];
    }

    /**
     * @return string|null A UUID For this object.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null The name of the large-event that this schedule is for.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string|null A User provided description of what the large-event is. (optional)
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string|null A URI to an image that can be used to represent this event. (optional)
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @return array<DateTime> A list of dates that this event occurs on.
     */
    public function getDays(): array
    {
        return $this->days;
    }

    /**
     * @return string|null $location An address for the location, if applicable. (optional)
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return array<string> A list of meta-data descriptions for the schedule.
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    function __toString()
    {
        return "Schedule:$this->id";
    }
}
