<?php

namespace Tapalava\Cassandra;

use Cassandra\Collection;
use Cassandra\Type;

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
     * @param \Cassandra\Cassandra\Type $type Cassandra Type to define the
     *        collection as, see constants in `Cassandra::TYPE_*`
     *        (NOTE: This is typehinted incorrectly to match the incorrectly
     *        documented API in the cassandra sdk)
     * @param array $values Values to be inserted into the collection.
     * @return Collection A Cassandra Collection of the specified type with the
     *         specified values inserted into it.
     */
    public static function fromArray(Type $type, array $values): Collection
    {
        $collection = new Collection($type);
        if (0 !== count($values)) {
            $collection->add(...$values);
        }

        return $collection;
    }
}
