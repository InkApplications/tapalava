<?php

namespace Tapalava\AuthenticationBundle\Command;

use Cassandra\SimpleStatement;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command line script to drop all user tables.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class DatabaseDropCommand extends Command
{
    /**
     * @var Client For accessing schedule data.
     */
    public $client;

    protected function configure()
    {
        $this->setName('database:drop-user');
        $this->setDescription('Drop the user database tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Dropping User Profile Table');
        $this->dropProfile();

        $output->writeln('Dropping user Credentials Table');
        $this->dropCredentials();
    }

    private function dropProfile()
    {
        $this->client->execute(new SimpleStatement('DROP TABLE profile;'));
    }

    private function dropCredentials()
    {
        $this->client->execute(new SimpleStatement('DROP TABLE credentials;'));
    }
}
