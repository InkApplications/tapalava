<?php

namespace Tapalava\Event;

/**
 * Service for looking up/saving information about schedules in the system.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
interface EventRepository
{
    /**
     * Find a specific event by its ID.
     *
     * @param string $id The Unique identifier of the Event to find.
     * @return Event The Event matching the ID provided.
     * @throws EventNotFoundException If the event ID doesn't exist.
     */
    public function find($id): Event;

    /**
     * Find all events associated with a specified schedule.
     *
     * @param string $scheduleId The ID of the schedule to find events for.
     * @return array The events associated with the provided schedule ID. Empty
     *               if none found, never null.
     */
    public function findAll($scheduleId): array;

    /**
     * Persist a new Event to application storage.
     *
     * @param Event $event the new event to update in the data storage.
     * @return string The ID of the event saved (this will be generated if new)
     */
    public function save(Event $event);
}
