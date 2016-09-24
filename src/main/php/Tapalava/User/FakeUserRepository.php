<?php

namespace Tapalava\User;

use InkApplications\Knock\User\TemporaryPasswordUser;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * A fake user repository that always returns a user of `johndoe@tapalava.com`
 * with password `abc-123`.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class FakeUserRepository implements UserRepository
{
    public function saveUserCredentials(TemporaryPasswordUser $user) {}

    public function findCredentialsByEmail($email) : TemporaryPasswordUser
    {
        return new User('fake-user-001', 'johndoe@tapalava.com', ['ROLE_ADMIN'], '$2y$13$.seDcm.uabVc6HvQshpp7.9fQJrChYuid6zRvZD0BXtegOPV0Aja2', 'def-456', new DateTime());
    }
}
