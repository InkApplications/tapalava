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
    public function find($id) : Event;

    /**
     * Find all events associated with a specified schedule.
     *
     * @param string $scheduleId The ID of the schedule to find events for.
     * @return array The events associated with the provided schedule ID. Empty
     *               if none found, never null.
     */
    public function findAll($scheduleId) : array;

    /**
     * Persist a new Event to application storage.
     *
     * @param Event $event the new event to create in the data storage.
     * @return Event The persisted event with updated information that may have
     *               been changed by the data storage, such as an
     *               auto-generated ID.
     */
    public function create(Event $event) : Event;

    /**
     * Persist updated information to an existing Event in application storage.
     *
     * @param Event $event the event to update in the data storage.
     * @return Event The persisted event with updated information that may have
     *               been changed by the data storage, such as a timestamp.
     */
    public function update(Event $event) : Event;
}
