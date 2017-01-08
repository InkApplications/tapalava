<?php

namespace Tapalava\User;

use Cassandra\ExecutionOptions;
use Cassandra\SimpleStatement;
use Cassandra\Timestamp;
use Cassandra\Type;
use Cassandra\Uuid;
use DateTime;
use InkApplications\Knock\User\CredentialsNotFoundException;
use InkApplications\Knock\User\TemporaryPasswordUser;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Tapalava\Cassandra\CollectionFactory;
use Tapalava\Cassandra\Row;

/**
 * Lookup and Persistence of User information into a cassandra database.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class CassandraUserRepository implements UserRepository, UserProviderInterface
{
    private $client;

    /**
     * @param $client Cassandra data connection.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function saveCredentials(Credentials $user)
    {
        $passwordCreated = $user->getPasswordCreated();
        $passwordCreatedTimestamp = null === $passwordCreated ? null : new Timestamp($passwordCreated->getTimestamp());

        $statement = new SimpleStatement('
            INSERT INTO credentials (
                email,
                profile_id,
                roles,
                password,
                salt,
                password_created
            )
            VALUES (?, ?, ?, ?, ?, ?)
        ');

        $options = new ExecutionOptions(['arguments' => [
            'email' => $user->getEmail(),
            'profile_id' => $user->getUsername(),
            'roles' => CollectionFactory::fromArray(Type::text(), $user->getRoles()),
            'password' => $user->getPassword(),
            'salt' => $user->getSalt(),
            'password_created' => $passwordCreatedTimestamp,
        ]]);

        $this->client->execute($statement, $options);
    }

    public function findCredentialsByEmail($email): TemporaryPasswordUser
    {
        $email = strtolower($email);
        $statement = new SimpleStatement('SELECT * FROM credentials WHERE email=?');
        $options = new ExecutionOptions(['arguments' => ['email' => $email]]);

        $results = $this->client->execute($statement, $options);

        if ($results->count() == 0) {
            throw new CredentialsNotFoundException($email);
        }

        return $this->credentialsFromRow(new Row($results->first()));
    }

    public function createUser($email): TemporaryPasswordUser
    {
        $uuid = new Uuid();
        $email = strtolower($email);

        return new Credentials($uuid->uuid(), $email, ['ROLE_USER']);
    }

    public function loadUserByUsername($username)
    {
        return $this->findCredentialsByEmail($username);
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    public function supportsClass($class)
    {
        return $class instanceof Credentials;
    }

    public function updateUserCredentials(TemporaryPasswordUser $user, $password, $salt, DateTime $passwordCreated)
    {
        $updated = new Credentials(
            $user->getUsername(),
            $user->getEmail(),
            $user->getRoles(),
            $password,
            $salt,
            $passwordCreated
        );

        $this->saveCredentials($updated);
    }

    public function destroyUserCredentials(TemporaryPasswordUser $user)
    {
        $updated = new Credentials(
            $user->getUsername(),
            $user->getEmail(),
            $user->getRoles()
        );

        $this->saveCredentials($updated);
    }

    /**
     * Transform a cassandra row into a data model.
     *
     * @param Row $row A single row returned from a cassandra lookup.
     * @return Credentials A user object created from the row's columns
     */
    private function credentialsFromRow(Row $row): Credentials
    {
        $user = new Credentials(
            $row->get('profile_id'),
            $row->get('email'),
            $row->getOptionalCollectionValues('roles'),
            $row->getOptional('password'),
            $row->getOptional('salt'),
            $row->getOptionalDateTime('password_created')
        );

        return $user;
    }
}
