<?php

namespace Tapalava\User;

use DateTime;
use InkApplications\Knock\User\TemporaryPasswordUser;

/**
 * User Authentication and Authorization records.
 *
 * Unlike a Profile, multiple users may have multiple Credentials. Credentials
 * only indicate a user's login auth information.
 *
 * @see Profile
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class Credentials implements TemporaryPasswordUser
{
    /** @var string|null A unique Identifier for the user. */
    private $username;

    /** @var string|null User-entered email address when signing up. */
    private $email;

    /** @var array Special authorization roles given to the user. */
    private $roles = [];

    /** @var string|null The hashed version of the user's password. */
    private $password;

    /** @var string|null The salt used when generating the user's password hash. */
    private $salt;

    /** @var DateTime|null The timestamp of when the user's password was generated. */
    private $passwordCreated;

    /**
     * @param string|null $username A unique Identifier for the user.
     * @param string|null $email User-entered email address when signing up.
     * @param array $roles Special authorization roles given to the user.
     * @param null $password The hashed version of the user's password.
     * @param null $salt The salt used when generating the user's password hash.
     * @param DateTime|null $passwordCreated The timestamp of when the user's password was generated.
     */
    public function __construct(
        $username = null,
        $email = null,
        array $roles = null,
        $password = null,
        $salt = null,
        DateTime $passwordCreated = null
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->roles = $roles ?: [];
        $this->password = $password;
        $this->salt = $salt;
        $this->passwordCreated = $passwordCreated;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPasswordCreated(): ?DateTime
    {
        return $this->passwordCreated;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function eraseCredentials() {}
}
