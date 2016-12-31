<?php

namespace Tapalava\Cassandra;

use Cassandra;
use Cassandra\Collection;
use Cassandra\Timestamp;
use DateTime;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;

class RowTest extends TestCase
{
    /**
     * Tow should be able to get and set properties implementing ArrayAccess.
     *
     * @test
     */
    public function arrayAccess()
    {
        $row = new Row(['foo' => 'bar', 'baz' => 5]);

        $this->assertTrue($row->offsetExists('foo'));
        $this->assertTrue($row->offsetExists('baz'));
        $this->assertFalse($row->offsetExists('bar'));
        $this->assertEquals('bar', $row->offsetGet('foo'));
        $this->assertEquals(5, $row->offsetGet('baz'));

        $row->offsetSet('qux', 'quux');
        $this->assertEquals('quux', $row->offsetGet('qux'));

        $row->offsetUnset('qux');
        $this->assertFalse($row->offsetExists('qux'));

        $row->offsetUnset('missing');
    }

    /**
     * Getting an undefined index results in an exception.
     *
     * @test
     * @expectedException \Tapalava\Cassandra\ColumnNotFoundException
     */
    public function arrayAccessUndefinedIndex()
    {
        $row = new Row();
        $row->offsetGet('foo');
    }

    /**
     * Getting an undefined column results in an exception.
     *
     * @test
     * @expectedException \Tapalava\Cassandra\ColumnNotFoundException
     */
    public function getUndefinedIndex()
    {
        $row = new Row();
        $row->get('foo');
    }

    /**
     * Getting an optional value can return the result or null if it doesn't exist.
     *
     * @test
     */
    public function getOptional()
    {
        $row = new Row(['bar' => 'baz']);

        $this->assertEquals('baz', $row->getOptional('bar'));
        $this->assertNull($row->getOptional('foo'));
    }

    /**
     * Getting collection values from a cassandra collection results in a matching php array.
     *
     * @test
     * @dataProvider validCollections
     */
    public function getCollectionValues($collection, $values)
    {
        $row = new Row(['test_collection' => $collection]);

        $result = $row->getOptionalCollectionValues('test_collection');

        $this->assertEquals($values, $result);
    }

    /**
     * Getting a timestamp value from a cassandra column results in a valid datetime object.
     *
     * @test
     * @dataProvider validTimestamps
     */
    public function getDateTimeValue($timestamp, $datetime)
    {
        $row = new Row(['test_timestamp' => $timestamp]);

        $result = $row->getOptionalDateTime('test_timestamp');

        $this->assertEquals($datetime, $result);
    }

    /**
     * When getting an object of a specified type, the result should match the object type.
     *
     * @test
     */
    public function getType()
    {
        $type = new FakeTestType();
        $row = new Row(['test_type' => $type]);

        $result = $row->getOptionalColumnType('test_type', FakeTestType::class);

        $this->assertSame($result, $type);
        $this->assertInstanceOf(FakeTestType::class, $result);
    }

    /**
     * When attempting to get an object that does not match the specified type, an exception is thrown.
     *
     * @test
     * @expectedException \Tapalava\Cassandra\UnexpectedColumnType
     */
    public function getDisparateType()
    {
        $row = new Row(['test_type' => new FakeTestType()]);

        $row->getOptionalColumnType('test_type', stdClass::class);
    }

    public static function validCollections()
    {
        $emptyCollection = new Collection(Cassandra::TYPE_VARCHAR);
        $fullCollection = new Collection(Cassandra::TYPE_VARCHAR);
        $fullCollection->add('foo', 'bar');
        $intCollection = new Collection(Cassandra::TYPE_INT);
        $intCollection->add(8, 6);

        return [
            [
                'collection' => $emptyCollection,
                'values' => [],
            ],
            [
                'collection' => null,
                'values' => null,
            ],
            [
                'collection' => $fullCollection,
                'values' => ['foo', 'bar'],
            ],
            [
                'collection' => $intCollection,
                'values' => [8, 6],
            ],
        ];
    }

    public static function validTimestamps()
    {
        return [
            [
                'timestamp' => null,
                'datetime' => null,
            ],
            [
                'timestamp' => new Timestamp('1460207655'),
                'datetime' => new DateTime('@1460207655'),
            ],
        ];
    }
}

/**
 * Used for testing `getType` methods
 */
class FakeTestType {}
