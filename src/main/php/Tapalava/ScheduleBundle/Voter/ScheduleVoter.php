<?php

namespace Tapalava\ScheduleBundle\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Tapalava\Schedule\Schedule;
use Tapalava\User\User;

/**
 * Votes on a given logged-in user's access to a specific schedule.
 *
 * Users are granted all access if they are in the schedule's admin users list.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class ScheduleVoter extends Voter
{
    protected function supports($attribute, $subject)
    {
        if ($subject instanceof Schedule) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        /** @var User $user */
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        $userId = $user->getId();

        return in_array($userId, $subject->getAdminUsers());
    }
}
