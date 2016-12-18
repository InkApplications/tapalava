<?php

namespace Tapalava\User;

use DateTime;
use InkApplications\Knock\User\TemporaryPasswordUser;

/**
 * Information and credentials for any system user.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class User implements TemporaryPasswordUser
{
    /** @var string|null A unique Identifier for the user. */
    private $id;

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
     * @param string|null $id A unique Identifier for the user.
     * @param string|null $email User-entered email address when signing up.
     * @param array $roles Special authorization roles given to the user.
     * @param null $password The hashed version of the user's password.
     * @param null $salt The salt used when generating the user's password hash.
     * @param DateTime|null $passwordCreated The timestamp of when the user's password was generated.
     */
    public function __construct(
        $id = null,
        $email = null,
        array $roles = [],
        $password = null,
        $salt = null,
        DateTime $passwordCreated = null
    ) {
        $this->id = $id;
        $this->email = $email;
        $this->roles = $roles;
        $this->password = $password;
        $this->salt = $salt;
        $this->passwordCreated = $passwordCreated;
    }

    /**
     * @return string|null A unique Identifier for the user.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null User-entered email address when signing up.
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function getRoles() : array
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

    public function getPasswordCreated()
    {
        return $this->passwordCreated;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setPasswordCreated(DateTime $created)
    {
        $this->passwordCreated = $created;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        $this->password = null;
        $this->salt = null;
        $this->passwordCreated = null;
    }
}
