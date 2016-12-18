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
        $output->writeln('Dropping user Table');
        $this->dropUserTable();

        $output->writeln('Dropping user email ref Table');
        $this->dropUserByEmailRefTable();
    }

    private function dropUserTable()
    {
        $this->client->execute(new SimpleStatement('DROP TABLE user;'));
    }

    private function dropUserByEmailRefTable()
    {
        $this->client->execute(new SimpleStatement('DROP TABLE user_by_email;'));
    }
}
