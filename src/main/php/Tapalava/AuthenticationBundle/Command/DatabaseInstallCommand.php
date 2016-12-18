<?php

namespace Tapalava\AuthenticationBundle\Command;

use Cassandra\SimpleStatement;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command line script to install any necessary tables for the schedule bundle.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class DatabaseInstallCommand extends Command
{
    /** @var Client For accessing schedule data. */
    public $client;

    protected function configure()
    {
        $this->setName('database:install-user');
        $this->setDescription('install the user database tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating User Table');
        $this->createUserTable();

        $output->writeln('Creating User email Ref Table');
        $this->createUserByEmailRefTable();
    }

    private function createUserTable()
    {
        $this->client->execute(new SimpleStatement('
            CREATE TABLE user (
                id text,
                email text,
                roles list<text>,
                password text,
                salt text,
                password_created timestamp,
                PRIMARY KEY (id)
            );
        '));
    }

    private function createUserByEmailRefTable()
    {
        $this->client->execute(new SimpleStatement('
            CREATE TABLE user_by_email (
                id text,
                email text,
                roles list<text>,
                password text,
                salt text,
                password_created timestamp,
                PRIMARY KEY (email)
            );
        '));
    }
}
