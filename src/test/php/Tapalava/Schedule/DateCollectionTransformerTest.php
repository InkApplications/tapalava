<?php

namespace Tapalava\Schedule;

use Cassandra;
use Cassandra\Collection;
use PHPUnit_Framework_TestCase as TestCase;
use DateTime;

class DateCollectionTransformerTest extends TestCase
{
    /**
     * Transform a Cassandra collection of strings to an array of DateTime objects.
     *
     * @dataProvider validDates
     * @test
     */
    public function toArray($collection, $array)
    {
        $test = new DateCollectionTransformer();

        $result = $test->toArray($collection);

        $this->assertEquals($array, $result);
    }

    /**
     * Transform an array of DateTime objects into a Cassandra collection of strings.
     *
     * @dataProvider validDates
     * @test
     */
    public function toCollection($collection, $array)
    {
        $test = new DateCollectionTransformer();

        $result = $test->toCollection($array);

        $this->assertEquals($collection, $result);
    }


    public static function validDates(): array
    {
        $singleCollection = new Collection(Cassandra::TYPE_VARCHAR);
        $singleCollection->add('1991-04-09');

        $doubleCollection = new Collection(Cassandra::TYPE_VARCHAR);
        $doubleCollection->add('1991-04-09');
        $doubleCollection->add('1991-04-10');
        return [
            [
                null,
                null,
            ],
            [
                new Collection(Cassandra::TYPE_VARCHAR),
                [],
            ],
            [
                $singleCollection,
                [new DateTime('1991-04-09')],
            ],
            [
                $doubleCollection,
                [new DateTime('1991-04-09'), new DateTime('1991-04-10')],
            ],
        ];
    }
}
