<?php

namespace Tapalava\Cassandra;

use ArrayAccess;
use Cassandra\Collection;
use Cassandra\Timestamp;
use DateTime;

/**
 * Wraps a cassandra data row and provides methods for accessing the data inside.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class Row implements ArrayAccess
{
    private $rowData;

    public function __construct(array $rowData = null)
    {
        $this->rowData = $rowData ?? [];
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->rowData[$offset]);
    }

    /**
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     * @throws ColumnNotFoundException
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->rowData[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->rowData[$offset]);
    }

    /**
     * Get the value for column in the row.
     *
     * @param string $key The column name to get
     * @return mixed the value of the column, this may be a primitive or a
     *         cassandra datatype.
     * @throws ColumnNotFoundException
     */
    public function get($key)
    {
        if (false === $this->offsetExists($key)) {
            throw new ColumnNotFoundException('Column `' . $key . '`  was not found in row');
        }
        return $this->rowData[$key];
    }

    /**
     * Get the value for a column in the row, if it exists.
     *
     * @param string $key The column name to get
     * @return mixed|null The value of the column, this may be a primitive or a
     *         cassandra data type. Null if the column was undefined.
     */
    public function getOptional($key)
    {
        return $this->rowData[$key] ?? null;
    }

    /**
     * Get an array of values from a Cassandra Collection field.
     *
     * @param string $key The collection's column name to get.
     * @return array All of the values in the collection as an array. If the
     *         column does not exist, this returns null.
     * @throws UnexpectedColumnType If the column requested was not a collection.
     */
    public function getOptionalCollectionValues($key): ?array
    {
        /** @var Collection $column */
        $column = $this->getOptionalColumnType($key, Collection::class);

        if (null == $column) {
            return null;
        }

        return $column->values();
    }

    /**
     * Get a DateTime object from a Cassandra Timestamp.
     *
     * @param string $key The Timestamp's column name to get.
     * @return DateTime|null
     * @throws UnexpectedColumnType If the column requested was not a Timestamp.
     */
    public function getOptionalDateTime($key): ?DateTime
    {
        /** @var Timestamp $column */
        $column = $this->getOptionalColumnType($key, Timestamp::class);

        if (null == $column) {
            return null;
        }

        return new DateTime('@' . $column->time());
    }

    /**
     * Get a column value of a specified class type.
     *
     * @param string $key The column name to get.
     * @param string $class The Fully-Qualified Class name of the class that
     *        the column value is expected to match.
     * @return mixed|null The column value matching the specified class, or null
     *         if the column did not exist.
     * @throws UnexpectedColumnType if the column value does not match
     *         the expected type.
     */
    public function getOptionalColumnType($key, $class)
    {
        $column = $this->getOptional($key);

        if (null === $column) {
            return null;
        }

        if ($column instanceof $class) {
            return $column;
        }

        throw new UnexpectedColumnType('Column returned unexpected type. Expected: `' . $class . '``, got: `' . get_class($column) . '``');
    }
}
