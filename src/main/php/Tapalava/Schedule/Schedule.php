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
     * Schedule constructor.
     * @param string $id
     * @param string $name
     * @param string $description
     * @param string $banner
     * @param array $days
     * @param string $location
     * @param array $tags
     */
    public function __construct($id = null, $name = null, array $days = null, $description = null, $banner = null, $location = null, array $tags = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->banner = $banner;
        $this->days = $days ?: [];
        $this->location = $location;
        $this->tags = $tags ?: [];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * @return array<DateTime>
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @return array<string>
     */
    public function getTags()
    {
        return $this->tags;
    }

    function __toString()
    {
        return "Schedule:$this->id";
    }
}
