<?php

namespace Tapalava\ScheduleBundle\Command;

use Cassandra\SimpleStatement;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Install any necessary tables for the schedule bundle.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class DatabaseInstallCommand extends Command
{
    /** @var Client For accessing schedule data. */
    public $client;

    protected function configure()
    {
        $this->setName('database:install-schedule');
        $this->setDescription('install the schedule database tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Creating Schedule Table');
        $this->createScheduleTable();
    }

    private function createScheduleTable()
    {
        $this->client->execute(new SimpleStatement('
            CREATE TABLE schedule (
                id text,
                name text,
                days list<text>,
                description text,
                banner text,
                location text,
                tags list<text>,
                created timestamp,
                PRIMARY KEY (id, created)
            );
        '));
    }
}
