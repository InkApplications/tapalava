<?php

namespace Tapalava\Event;

use DateTime;

/**
 * Represents a single event for a schedule.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class Event
{
    /** @var null|string UUID for this object. */
    private $id;

    /** @var null|string A UUID for the schedule that this event takes place in. */
    private $scheduleId;

    /** @var null|string User-defined name for the event. */
    private $name;

    /** @var DateTime|null When the event starts. */
    private $start;

    /** @var DateTime|null When the event ends. */
    private $end;

    /** @var null|string A main category to group this event with. */
    private $category;

    /** @var array Misc. user-defined metadata for the event. */
    private $tags;

    /** @var null|string What room the event is held in. */
    private $room;

    /** @var array Who is speaking or facilitating at the event. */
    private $hosts;

    /** @var null|string A user-defined description of the event. */
    private $description;

    /** @var null|string A background banner image to use as a backdrop. */
    private $banner;

    /**
     * @param null $id UUID for this object.
     * @param null $scheduleId A UUID for the schedule that this event takes place in.
     * @param null $name User-defined name for the event.
     * @param null $start When the event starts.
     * @param null $end When the event ends.
     * @param null $category A main category to group this event with.
     * @param array|null $tags Misc. user-defined metadata for the event.
     * @param null $room What room the event is held in.
     * @param array|null $hosts Who is speaking or facilitating at the event.
     * @param null $description A user-defined description of the event.
     * @param null $banner A background banner image to use as a backdrop.
     */
    public function __construct(
        $id = null,
        $scheduleId = null,
        $name = null,
        $start = null,
        $end = null,
        $category = null,
        array $tags = null,
        $room = null,
        array $hosts = null,
        $description = null,
        $banner = null
    ) {
        $this->id = $id;
        $this->scheduleId = $scheduleId;
        $this->name = $name;
        $this->start = $start;
        $this->end = $end;
        $this->category = $category;
        $this->tags = $tags ?: [];
        $this->room = $room;
        $this->hosts = $hosts ?: [];
        $this->description = $description;
        $this->banner = $banner;
    }

    /**
     * @return null|string UUID for this object.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return null|string A UUID for the schedule that this event takes place in.
     */
    public function getScheduleId()
    {
        return $this->scheduleId;
    }

    /**
     * @return null|string User-defined name for the event.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return DateTime|null When the event starts.
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return DateTime|null When the event ends.
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return null|string A main category to group this event with.
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @return array Misc. user-defined metadata for the event. Empty if
     *               none defined, never null.
     */
    public function getTags() : array
    {
        return $this->tags;
    }

    /**
     * @return null|string What room the event is held in.
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @return array Who is speaking or facilitating at the event. Empty if
     *               none defined, never null.
     */
    public function getHosts() : array
    {
        return $this->hosts;
    }

    /**
     * @return null|string A user-defined description of the event.
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return null|string A background banner image to use as a backdrop.
     */
    public function getBanner()
    {
        return $this->banner;
    }
}
