<?php

namespace Tapalava\User;

/**
 * Profile information about a user.
 *
 * Unlike credentials, these records are unique to a specific person.
 *
 * @see Credentials
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class Profile
{
    /** @var string|null A unique Identifier for the user's profile. */
    private $id;

    /** @var array A list of emails that the user has used as credentials. */
    private $emails;

    /** @var null|string A name to display for the user. */
    private $name;

    /**
     * @param string|null $id A unique Identifier for the user's profile.
     * @param array|null $emails A list of emails that the user has used as credentials.
     * @param string|null $name A name to display for the user.
     */
    public function __construct($id = null, array $emails = null, $name = null)
    {
        $this->id = $id;
        $this->emails = $emails ?: [];
        $this->name = $name;
    }

    /**
     * @return string A unique Identifier for the user's profile.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return array A list of emails that the user has used as credentials.
     */
    public function getEmails(): array
    {
        return $this->emails;
    }

    /**
     * @return null|string A name to display for the user.
     */
    public function getName()
    {
        return $this->name;
    }
}
