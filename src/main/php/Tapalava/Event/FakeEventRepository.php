<?php

namespace Tapalava\Event;

use DateTime;
use Tapalava\Schedule\ScheduleNotFoundException;

/**
 * Rudimentary stubbed our implementation of an Event Repository.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class FakeEventRepository implements EventRepository
{
    private $fakeEvents;

    public function __construct()
    {
        $full = new Event('fake-event-id-001', 'fake-id-001', 'Fake Event', new DateTime("1991-04-09 07:15:00-5:00"),  new DateTime("1991-04-10 06:15:00-5:00"), 'category', ['tag a', 'tag b'], 'fake room', ['John Doe', 'Jane Doe'], 'This is a fake event description', 'http://google.com/google.png');
        $minimal = new Event('fake-event-id-002', 'fake-id-001');

        $this->fakeEvents = [$full, $minimal];
    }

    public function find($id) : Event
    {
        switch ($id) {
            case 'fake-event-id-001':
                return $this->fakeEvents[0];
            case 'fake-event-id-002':
                return $this->fakeEvents[1];
            default:
                throw new EventNotFoundException($id);
        }
    }

    public function findAll($scheduleId) : array
    {
        if ('fake-id-001' === $scheduleId) {
            return $this->fakeEvents;
        }

        throw new ScheduleNotFoundException($scheduleId);
    }

    public function create(Event $event) : Event
    {
        return $event;
    }

    public function update(Event $event) : Event
    {
        return $event;
    }
}
