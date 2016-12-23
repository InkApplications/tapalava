<?php

namespace Tapalava\Schedule;

use DateTime;

/**
 * A fake implementation of a schedule repository providing fake information.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class FakeScheduleRepository implements ScheduleRepository
{
    private $fakeSchedules;

    public function __construct()
    {
        $full = new Schedule("fake-id-001", "Full Schedule Example With a really long name to see if it breaks anything<script>alert('no HTML!');</script>", [new DateTime("1991-04-09"), new DateTime("1991-04-10")], "Donec sollicitudin *molestie* malesuada. **Praesent** sapien [massa](http://google.com), convallis a pellentesque nec, egestas non nisi. Curabitur aliquet quam id dui posuere blandit. Pellentesque in ipsum id orci porta dapibus. Curabitur arcu erat, accumsan id imperdiet et, porttitor at sem. Curabitur aliquet quam id dui posuere blandit. Proin eget tortor risus. Proin eget tortor risus. Praesent sapien massa, convallis a pellentesque nec, egestas non nisi. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae; Donec velit neque, auctor sit amet aliquam vel, ullamcorper sit amet ligula.<script>alert('no HTML!');</script>", "https://placekitten.com/550/300", "Minneapolis, MN", ["Foo", "Bar"]);
        $minimal = new Schedule("fake-id-002");

        $this->fakeSchedules = [$full, $minimal];
    }

    public function find($id): Schedule
    {
        if ($id === 'fake-id-001') {
            return $this->fakeSchedules[0];
        }

        if ($id === 'fake-id-002') {
            return $this->fakeSchedules[1];
        }

        throw new ScheduleNotFoundException($id);
    }

    public function findAll(): array
    {
        return $this->fakeSchedules;
    }

    public function save(Schedule $schedule)
    {
        return $schedule->getId() ?: 'fake-generated-id';
    }
}
