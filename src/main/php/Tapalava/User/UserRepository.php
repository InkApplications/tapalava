<?php

namespace Tapalava\User;

use InkApplications\Knock\User\UserRepository as KnockUserRepository;

/**
 * Service used for looking up information and collections of User objects.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
interface UserRepository extends KnockUserRepository
{
    /**
     * Save the user and/or its password credentials.
     *
     * This method is invoked after the user's password has been modified and
     * needs to be persisted to the application's data storage.
     *
     * @param Credentials $user The user/credentials that need to be saved.
     */
    public function saveCredentials(Credentials $user);
}
