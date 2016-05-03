<?php

namespace Tapalava\Schedule;

/**
 * Service for looking up/saving information about schedules in the system.
 *
 * @@author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
interface ScheduleRepository
{
    /**
     * Find a specific Schedule.
     *
     * @param string $id The UUID of the Schedule to look up.
     * @return Schedule The schedule in persistence matching the ID.
     * @throws ScheduleNotFoundException If a schedule with the specified ID was
     *                                   not found in persistence.
     */
    public function find($id): Schedule;

    /**
     * Find all Schedules.
     *
     * WARNING: This is potentially dangerous, this method should not be exposed
     * to public use.
     *
     * @return array<Schedule> All of the Schedule objects in persistence.
     */
    public function findAll(): array;

    /**
     * Save a Schedule into persistence.
     *
     * @param Schedule $schedule The full Schedule object to be persisted.
     * @return void
     */
    public function save(Schedule $schedule);
}
