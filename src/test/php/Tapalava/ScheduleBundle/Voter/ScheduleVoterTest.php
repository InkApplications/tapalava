<?php

namespace Tapalava\ScheduleBundle\Voter;

use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tapalava\Schedule\Schedule;
use Tapalava\User\User;

class ScheduleVoterTest extends TestCase
{
    /**
     * Access should be granted/rejected/abstained properly according to the
     * Schedule's admin field.
     *
     * @dataProvider votingResults
     * @test
     */
    public function vote($token, $subject, $expectedResult)
    {
        $voter = new ScheduleVoter();

        $result = $voter->vote($token, $subject, ['fake-attribute']);

        $this->assertEquals($expectedResult, $result);
    }

    /** Voting outcomes for given users and schedules. */
    public static function votingResults(): array
    {
        $schedule = new Schedule(null, null, null, null, null, null, null, ['fake-test-user']);

        $adminUser = new User('fake-test-user', 'testuser@tapalava.com', ['ROLE_USER']);
        $adminToken = new UsernamePasswordToken($adminUser, null, 'test');

        $randomUser = new User('random-test-user', 'testuser@tapalava.com', ['ROLE_USER']);
        $randomUserToken = new UsernamePasswordToken($randomUser, null, 'test');

        $anonymousToken = new AnonymousToken(null, 'anon', []);

        return [
            [
                'token' => $adminToken,
                'subject' => $schedule,
                'result' => ScheduleVoter::ACCESS_GRANTED,
            ],
            [
                'token' => $randomUserToken,
                'subject' => $schedule,
                'result' => ScheduleVoter::ACCESS_DENIED,
            ],
            [
                'token' => $anonymousToken,
                'subject' => $schedule,
                'result' => ScheduleVoter::ACCESS_DENIED,
            ],
            [
                'token' => $anonymousToken,
                'subject' => new \stdClass(),
                'result' => ScheduleVoter::ACCESS_ABSTAIN,
            ],
        ];
    }
}
