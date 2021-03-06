<?php

namespace Tapalava\ScheduleBundle\Command;

use Cassandra\SimpleStatement;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Drop all schedule tables.
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
        $this->setName('database:drop-schedule');
        $this->setDescription('Drop the schedule database tables');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Dropping Schedule Table');
        $this->dropScheduleTable();
        $output->writeln('Dropping Event Table');
        $this->dropEventTable();
    }

    private function dropScheduleTable()
    {
        $this->client->execute(new SimpleStatement('
            DROP TABLE schedule;
        '));
    }

    private function dropEventTable()
    {
        $this->client->execute(new SimpleStatement('
            DROP TABLE event;
        '));
    }
}
