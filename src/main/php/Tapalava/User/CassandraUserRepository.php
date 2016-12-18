<?php

namespace Tapalava\User;

use Cassandra;
use Cassandra\BatchStatement;
use Cassandra\Collection;
use Cassandra\ExecutionOptions;
use Cassandra\SimpleStatement;
use Cassandra\Timestamp;
use Cassandra\Uuid;
use DateTime;
use InkApplications\Knock\User\CredentialsNotFoundException;
use InkApplications\Knock\User\TemporaryPasswordUser;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use TypeError;

/**
 * Lookup and Persistence of User information into a cassandra database.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class CassandraUserRepository implements UserRepository, UserProviderInterface
{
    private $client;

    /**
     * CassandraUserRepository constructor.
     * @param $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Save the user and/or its password credentials.
     *
     * This method is invoked after the user's password has been modified and
     * needs to be persisted to the application's data storage.
     *
     * @param TemporaryPasswordUser $user The user/credentials that need to be saved.
     * @throws TypeError If this is given a differnent user object than our local one.
     */
    public function saveUserCredentials(TemporaryPasswordUser $user)
    {
        /** @var User $user */
        if (false === $user instanceof User) {
            throw new TypeError();
        }

        $roles = new Collection(Cassandra::TYPE_VARCHAR);
        $roles->add(...$user->getRoles());

        $passwordCreated = $user->getPasswordCreated();
        $passwordCreatedTimestamp = null === $passwordCreated ? null : new Timestamp($passwordCreated->getTimestamp());

        $batch = new BatchStatement(Cassandra::BATCH_LOGGED);
        $userStatement = $this->client->prepare('
            INSERT INTO user (
                id,
                email,
                roles,
                password,
                salt,
                password_created
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $emailStatement = $this->client->prepare('
            INSERT INTO user_by_email (
                id,
                email,
                roles,
                password,
                salt,
                password_created
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        $arguments = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'roles' => $roles,
            'password' => $user->getPassword(),
            'salt' => $user->getSalt(),
            'password_created' => $passwordCreatedTimestamp,
        ];

        $batch->add($userStatement, $arguments);
        $batch->add($emailStatement, $arguments);
        $this->client->execute($batch);
    }

    /**
     * Find user credentials by an email address.
     *
     * @param string $email The unique email address of the user to lookup.
     * @return TemporaryPasswordUser A user that matches the given email.
     * @throws CredentialsNotFoundException If no user matching the given email
     *         Could be found.
     */
    public function findCredentialsByEmail($email): TemporaryPasswordUser
    {
            $statement = new SimpleStatement('SELECT * FROM user_by_email WHERE email=?');
            $options = new ExecutionOptions(['arguments' => ['email' => $email]]);

            $results = $this->client->execute($statement, $options);

            if ($results->count() == 0) {
                throw new CredentialsNotFoundException($email);
            }

            return $this->fromRow($results->first());
    }

    /**
     * Create a new local user object to be persisted.
     *
     * @param string $email The user's email address to base the user object on.
     * @return TemporaryPasswordUser A newly created user-model to be persisted.
     */
    public function createUser($email): TemporaryPasswordUser
    {
        $uuid = new Uuid();

        return new User($uuid->uuid(), $email, ['ROLE_USER']);
    }

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
        return $this->findCredentialsByEmail($username);
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
        return $class instanceof User;
    }

    /**
     * Transform a cassandra row into a data model.
     *
     * @param array $row A single row returned from a cassandra lookup.
     * @return User
     */
    private function fromRow(array $row)
    {
        $roles = [];
        if (isset($row['roles']) && $row['roles'] !== null) {
            $roles = $row['roles']->values();
        }

        $passwordCreated = null;
        if (isset($row['password_created']) && $row['password_created'] !== null) {
            $passwordCreated = new DateTime('@' . $row['password_created']);
        }

        $user = new User(
            $row['id'] ?? null,
            $row['email'] ?? null,
            $roles,
            $row['password'] ?? null,
            $row['salt'] ?? null,
            $passwordCreated
        );

        return $user;
    }
}
