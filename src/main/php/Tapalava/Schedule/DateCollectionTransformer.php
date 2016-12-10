<?php

namespace Tapalava\Schedule;

use Cassandra;
use Cassandra\Collection;
use DateTime;

/**
 * Transform collections of Dates to and from Cassandra collections.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class DateCollectionTransformer
{
    /**
     * Transform a DateTime array into a Cassandra collection of strings.
     *
     * @param array|null $dates An array of DateTime objects to be transformed.
     * @return Collection|null A cassandra collection matching the provided
     *                         array, in order. Null if the array was also null.
     */
    public function toCollection(array $dates = null)
    {
        if (null === $dates) {
            return null;
        }

        $serialized = new Collection(Cassandra::TYPE_VARCHAR);

        foreach ($dates as $key => $date) {
            $serialized->add($date->format('Y-m-d'));
        }

        return $serialized;
    }

    /**
     * Transform a Cassandra string collection into an array of DateTime objects.
     *
     * @param Collection|null $collection A cassandra collection of date strings.
     * @return array|null An array of DateTime objects matching the provided
     *                    collection, in order. Null if the collection provided
     *                    was also null.
     */
    public function toArray(Collection $collection = null)
    {
        if (null === $collection) {
            return null;
        }

        $dates = [];

        foreach ($collection as $date) {
            $dates[] = new DateTime($date);
        }

        return $dates;
    }
}
