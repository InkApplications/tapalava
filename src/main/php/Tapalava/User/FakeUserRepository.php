<?php

namespace Tapalava\User;

use InkApplications\Knock\User\TemporaryPasswordUser;
use DateTime;

/**
 * A fake user repository that always returns a user of `johndoe@tapalava.com`
 * with password `abc-123`.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 * @codeCoverageIgnore
 */
class FakeUserRepository implements UserRepository
{
    public function saveUserCredentials(TemporaryPasswordUser $user) {}

    public function findCredentialsByEmail($email): TemporaryPasswordUser
    {
        return new Credentials('fake-user-001', 'johndoe@tapalava.com', ['ROLE_ADMIN'], '$2y$13$.seDcm.uabVc6HvQshpp7.9fQJrChYuid6zRvZD0BXtegOPV0Aja2', 'def-456', new DateTime());
    }

    public function createUser($email): TemporaryPasswordUser
    {
        return new Credentials('fake-user-002', $email, ['ROLE_USER']);
    }

    public function updateUserCredentials(TemporaryPasswordUser $user, $password, $salt, DateTime $passwordCreated) {}

    public function destroyUserCredentials(TemporaryPasswordUser $user) {}

    public function saveCredentials(Credentials $user) {}
}
