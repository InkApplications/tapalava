<?php

namespace Tapalava\User;

use DateTime;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * A fake implementation of a user provider that returns an admin user with
 * password `abc-123`
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 * @codeCoverageIgnore
 */
class FakeUserProvider implements UserProviderInterface
{
    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        return new Credentials('fake-user-001', $username, ['ROLE_ADMIN'], '$2y$13$.seDcm.uabVc6HvQshpp7.9fQJrChYuid6zRvZD0BXtegOPV0Aja2', 'def-456', new DateTime());
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     *
     * @throws UnsupportedUserException if the account is not supported
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return 'Tapalava\User\Credentials' === $class;
    }
}
