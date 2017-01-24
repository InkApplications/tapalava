<?php

namespace Tapalava\Schedule;

use Cassandra;
use Cassandra\Collection;
use Cassandra\ExecutionOptions;
use Cassandra\Statement;
use Doctrine\Common\Collections\ArrayCollection;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use PHPUnit_Framework_TestCase as TestCase;
use Tapalava\User\CassandraUserRepository;
use Tapalava\User\Credentials;

class CassandraUserRepositoryTest extends TestCase
{
    /**
     * Make sure the repository can save models correctly.
     *
     * Right now this test can't assert anything, since the repository uses a
     * batch statement to execute the change, there's no way to spy on the
     * statement arguments. This could maybe be solved by adding a factory for
     * the batch statement in the function For now, this test just verifies
     * that there are no code errors in the function when run.
     *
     * @todo Find a way to add argument assertions to this test.
     * @test
     */
    public function saveUserCredentials()
    {
        $stubClient = new class($this) extends Client {
            private $test;
            public function __construct(TestCase $test) { $this->test = $test; }
            public function prepare($cql, ExecutionOptions $options = null) { return new Cassandra\SimpleStatement(''); }
            public function execute(Statement $statement, ExecutionOptions $options = null) { return null; }
        };

        $repository = new CassandraUserRepository($stubClient);
        $user = new Credentials('fake-id-001', 'tester@tapalava.com', ['ROLE_USER', 'ROLE_TEST'], 'test-password', 'salty');

        $repository->saveCredentials($user);
    }

    /**
     * Ensure the Temporary-password interface methods are returned properly.
     *
     * @test
     */
    public function findCredentialsByEmail()
    {
        $fakeClient = self::singleRowClient();
        $repository = new CassandraUserRepository($fakeClient);

        $result = $repository->findCredentialsByEmail('tester@tapalava.com');

        $this->assertEquals('test-password', $result->getPassword());
        $this->assertEquals('test-salt', $result->getSalt());
        $this->assertEquals('tester@tapalava.com', $result->getEmail());
        $this->assertEquals('test-id-001', $result->getUsername());
        $this->assertEquals(2, count($result->getRoles()));
        $this->assertEquals('role-a', $result->getRoles()[0]);
        $this->assertEquals('role-b', $result->getRoles()[1]);
        $this->assertEquals('2016-02-03 04:56:57', $result->getPasswordCreated()->format('Y-m-d H:i:s'));
    }

    /**
     * Ensure the Temporary-password interface methods are returned properly
     * even when null values are returned.
     *
     * @test
     */
    public function findNullCredentialsByEmail()
    {
        $fakeClient = self::singleNullRowClient();
        $repository = new CassandraUserRepository($fakeClient);

        $result = $repository->findCredentialsByEmail('tester@tapalava.com');

        $this->assertEquals('required-id', $result->getUsername());
        $this->assertEquals('required-email', $result->getEmail());
        $this->assertNull($result->getPassword());
        $this->assertNull($result->getSalt());
        $this->assertEquals(0, count($result->getRoles()));
        $this->assertNull($result->getPasswordCreated());
    }

    /**
     * Ensure the Temporary-password interface methods are returned properly
     * even when null values are returned.
     *
     * @test
     * @expectedException \InkApplications\Knock\User\CredentialsNotFoundException
     */
    public function findZeroCredentialsByEmail()
    {
        $fakeClient = self::zeroRowClient();
        $repository = new CassandraUserRepository($fakeClient);

        $repository->findCredentialsByEmail('tester@tapalava.com');
    }

    public function createUser()
    {
        $client = self::zeroRowClient();
        $repository = new CassandraUserRepository($client);

        /** @var Credentials $test */
        $test = $repository->createUser('tester@tapalava.com');

        $this->assertEquals('tester@tapalava.com', $test->getUsername());
        $this->assertInstanceOf(Credentials::class, $test);
        $this->assertNotNull($test->getUsername());
        $this->assertEquals(1, $test->getRoles());
        $this->assertEquals('ROLE_USER', $test->getRoles()[0]);
    }

    /** Client that will return a single fully-populated user row. */
    protected static function singleRowClient(): Client
    {
        return new class extends Client {
            public function __construct() {}
            public function execute(Statement $statement, ExecutionOptions $options = null) {

                $roles = new Collection(Cassandra::TYPE_VARCHAR);
                $roles->add('role-a');
                $roles->add('role-b');

                return new ArrayCollection([[
                    'profile_id' => 'test-id-001',
                    'email' => 'tester@tapalava.com',
                    'roles' => $roles,
                    'password' => 'test-password',
                    'salt' => 'test-salt',
                    'password_created' => new Cassandra\Timestamp(1454475417)
                ]]);
            }
        };
    }

    /** Client that will return a single non-populated user row. */
    protected static function singleNullRowClient(): Client
    {
        return new class extends Client {
            public function __construct() {}
            public function execute(Statement $statement, ExecutionOptions $options = null) {
                return new ArrayCollection([
                    [
                        'profile_id' => 'required-id',
                        'email' => 'required-email',
                    ]
                ]);
            }
        };
    }

    /** Client that will return no rows. */
    protected static function zeroRowClient(): Client
    {
        return new class extends Client {
            public function __construct() {}
            public function execute(Statement $statement, ExecutionOptions $options = null) {
                return new class {
                    public function count() { return 0; }
                    public function first() { return null; }
                };
            }
        };
    }
}
