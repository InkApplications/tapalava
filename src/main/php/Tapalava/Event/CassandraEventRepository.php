<?php

namespace Tapalava\Event;

use Cassandra;
use Cassandra\ExecutionOptions;
use Cassandra\SimpleStatement;
use Cassandra\Uuid;
use M6Web\Bundle\CassandraBundle\Cassandra\Client;
use Tapalava\Cassandra\CollectionFactory;
use Tapalava\Cassandra\Row;
use Tapalava\Schedule\ScheduleNotFoundException;

/**
 * Looks up event data from a cassandra database.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class CassandraEventRepository implements EventRepository
{
    private $client;

    /**
     * @param Client $client Cassandra connection client with an event table.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Find a specific event by its ID.
     *
     * @param string $scheduleId The parent schedule ID of the event to find.
     * @param string $id The Unique identifier of the Event to find.
     * @return Event The Event matching the ID provided.
     * @throws ScheduleNotFoundException
     */
    public function find($scheduleId, $id): Event
    {
        $statement = new SimpleStatement('SELECT * FROM event WHERE schedule_id=? AND id=?');
        $options = new ExecutionOptions(['arguments' => ['schedule_id' => $scheduleId, 'id' => $id]]);

        $results = $this->client->execute($statement, $options);

        if (count($results) == 0) {
            throw new ScheduleNotFoundException($id);
        }

        return $this->fromRow(new Row($results[0]));
    }

    /**
     * Find all events associated with a specified schedule.
     *
     * @param string $scheduleId The ID of the schedule to find events for.
     * @return Event[] The events associated with the provided schedule ID. Empty
     *               if none found, never null.
     */
    public function findAll($scheduleId): array
    {
        $statement = new SimpleStatement('SELECT * FROM event WHERE schedule_id=?');
        $options = new ExecutionOptions(['arguments' => ['schedule_id' => $scheduleId]]);

        $results = $this->client->execute($statement, $options);

        return $this->fromRows($results);
    }

    /**
     * Persist a new Event to application storage.
     *
     * @param Event $event the new event to update in the data storage.
     * @return string The ID of the event saved (this will be generated if new)
     */
    public function save(Event $event)
    {
        $id = $event->getId() ?: (new Uuid())->uuid();

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
            'schedule_id' => $event->getScheduleId(),
            'name' => $event->getName(),
            'start' => $event->getStart(),
            'end' => $event->getEnd(),
            'category' => $event->getCategory(),
            'tags' => CollectionFactory::fromArray(Cassandra::TYPE_VARCHAR, $event->getTags()),
            'room' => $event->getRoom(),
            'hosts' => CollectionFactory::fromArray(Cassandra::TYPE_VARCHAR, $event->getHosts()),
            'description' => $event->getDescription(),
            'banner' => $event->getBanner(),
        ]]);

        $this->client->execute($statement, $options);

        return $id;
    }

    /**
     * Transforms a cassandra data row into an Event object.
     *
     * @param Row $row A single result row from Cassandra data
     * @return Event The local model representing the data provided.
     */
    private function fromRow(Row $row): Event
    {
        return new Event(
            $row->get('id'),
            $row->get('schedule_id'),
            $row->getOptional('name'),
            $row->getOptionalDateTime('start'),
            $row->getOptionalDateTime('end'),
            $row->getOptional('category'),
            $row->getOptionalCollectionValues('tags'),
            $row->getOptional('room'),
            $row->getOptionalCollectionValues('hosts'),
            $row->getOptional('description'),
            $row->getOptional('banner')
        );
    }

    /**
     * Convert multiple cassandra row objects into an array of data models.
     *
     * @param \Iterator|array $rows an iterable set of rows returned from a
     *        cassandra query.
     * @return array An array of Event models matching the data provided.
     */
    private function fromRows($rows)
    {
        $parsed = [];
        foreach ($rows as $rowData) {
            $row = new Row($rowData);
            $parsed[] = $this->fromRow($row);
        }

        return $parsed;
    }
}
