<?php

namespace Tapalava\Schedule;
use Cassandra;
use Cassandra\Collection;
use Cassandra\ExecutionOptions;
use Cassandra\SimpleStatement;
use Cassandra\Uuid;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;

/**
 * Service for looking up/saving information about schedules in the system.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class CassandraScheduleRepository implements ScheduleRepository
{
    private $client;
    private $dateCollectionTransformer;

    /**
     * @param Client $client Cassandra connection client.
     * @param DateCollectionTransformer $dateCollectionTransformer Service for
     *        changing an array of DateTime objects into a cassandra collection
     *        of strings.
     */
    public function __construct(Client $client, DateCollectionTransformer $dateCollectionTransformer)
    {
        $this->client = $client;
        $this->dateCollectionTransformer = $dateCollectionTransformer;
    }

    /**
     * Find a specific Schedule.
     *
     * @param string $id The UUID of the Schedule to look up.
     * @return Schedule The schedule in persistence matching the ID.
     * @throws ScheduleNotFoundException If a schedule with the specified ID was
     *                                   not found in persistence.
     */
    public function find($id): Schedule
    {
        $statement = new SimpleStatement('SELECT * FROM schedule WHERE id=?');
        $options = new ExecutionOptions(['arguments' => ['id' => $id]]);

        $results = $this->client->execute($statement, $options);

        if (count($results) == 0) {
            throw new ScheduleNotFoundException($id);
        }

        return $this->fromRow($results[0]);
    }

    /**
     * Find all Schedules.
     *
     * WARNING: This is potentially dangerous, this method should not be exposed
     * to public use.
     *
     * @return array<Schedule> All of the Schedule objects in persistence.
     */
    public function findAll(): array
    {
        $statement = new SimpleStatement('SELECT * FROM schedule');

        $results = $this->client->execute($statement);

        return $this->fromRows($results);
    }

    /**
     * Save a Schedule into persistence.
     *
     * @param Schedule $schedule The full Schedule object to be persisted.
     * @return string
     */
    public function save(Schedule $schedule)
    {
        $id = $schedule->getId() ?: (new Uuid())->uuid();
        $days = $this->dateCollectionTransformer->toCollection($schedule->getDays());
        $tags = new Collection(Cassandra::TYPE_VARCHAR);
        if (0 !== count($schedule->getTags())) {
            $tags->add(...$schedule->getTags());
        }

        $statement = new SimpleStatement('
            INSERT INTO schedule (
                id,
                name,
                days,
                description,
                banner,
                location,
                tags,
                created
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, toTimestamp(now()))
        ');
        $options = new ExecutionOptions(['arguments' => [
            'id' => $id,
            'name' => $schedule->getName(),
            'days' => $days,
            'description' => $schedule->getDescription(),
            'banner' => $schedule->getBanner(),
            'location' => $schedule->getLocation(),
            'tags' => $tags,
        ]]);

        $this->client->execute($statement, $options);

        return $id;
    }

    private function fromRow(array $row): Schedule
    {
        $days = $this->dateCollectionTransformer->toArray($row['days'] ?? null);
        $tags = null;
        if (isset($row['tags']) && $row['tags'] != null) {
            $tags = $row['tags']->values();
        }

        return new Schedule(
            $row['id'],
            $row['name'] ?? null,
            $days,
            $row['description'] ?? null,
            $row['banner'] ?? null,
            $row['location'] ?? null,
            $tags
        );
    }

    private function fromRows(array $rows)
    {
        $parsed = [];
        foreach ($rows as $row) {
            $parsed[] = $this->fromRow($row);
        }

        return $parsed;
    }
}
