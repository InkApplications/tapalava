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
        $output->writeln('Creating User Profile Table');
        $this->createProfileTable();

        $output->writeln('Creating User Credentials Table');
        $this->createCredentialsTable();
    }

    private function createProfileTable()
    {
        $this->client->execute(new SimpleStatement('
            CREATE TABLE profile (
                id text,
                emails list<text>,
                name text,
                PRIMARY KEY (id)
            );
        '));
    }

    private function createCredentialsTable()
    {
        $this->client->execute(new SimpleStatement('
            CREATE TABLE credentials (
                email text,
                profile_id text,
                roles list<text>,
                password text,
                salt text,
                password_created timestamp,
                PRIMARY KEY (email)
            );
        '));
    }
}
