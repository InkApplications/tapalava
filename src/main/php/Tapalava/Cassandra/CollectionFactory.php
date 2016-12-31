<?php

namespace Tapalava\Cassandra;

use Cassandra\Collection;

/**
 * Creates Cassandra Collection objects from various data types.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class CollectionFactory
{
    /**
     * Create a Cassandra collection from a plain array.
     *
     * @param string $type Cassandra Type to define the collection as, see
     *        constants in `Cassandra::TYPE_*`
     * @param array $values Values to be inserted into the collection.
     * @return Collection A Cassandra Collection of the specified type with the
     *         specified values inserted into it.
     */
    public static function fromArray($type, array $values)
    {
        $collection = new Collection($type);
        if (0 !== count($values)) {
            $collection->add(...$values);
        }

        return $collection;
    }
}
