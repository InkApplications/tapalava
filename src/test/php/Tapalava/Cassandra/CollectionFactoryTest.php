<?php

namespace Tapalava\Cassandra;

use Cassandra;
use Cassandra\Collection;
use Cassandra\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;

class CollectionFactoryTest extends TestCase
{
    /**
     * Creating a collection with valid arguments produces an identical
     * Cassandra collection.
     *
     * @test
     * @dataProvider validArguments
     */
    public function testValidArguments($type, $values, $expectedCount)
    {
        $result = CollectionFactory::fromArray($type, $values);

        $this->assertNotNull($result);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($expectedCount, $result->count());

        foreach ($values as $offset => $value) {
            $this->assertEquals($result->get($offset), $value);
        }
    }

    /**
     * Attempting to create collections with illeal arguments produces exceptions.
     *
     * @test
     * @dataProvider invalidArguments
     */
    public function testInvalidArguments($type, $values, $exception)
    {
        $this->setExpectedException($exception);
        CollectionFactory::fromArray($type, $values);
    }

    /**
     * Valid arguments and expectations for producing Collections.
     */
    public static function validArguments()
    {
        return [
            [
                'type' => Cassandra::TYPE_INT,
                'values' => [6, 5, 2],
                'expectedCount' => 3
            ],
            [
                'type' => Cassandra::TYPE_VARCHAR,
                'values' => ['foo'],
                'expectedCount' => 1
            ],
            [
                'type' => Cassandra::TYPE_DOUBLE,
                'values' => [],
                'expectedCount' => 0
            ],
        ];
    }

    /**
     * Arguments that will produce exceptions when attempted.
     */
    public static function invalidArguments()
    {
        return [
            [
                'type' => Cassandra::TYPE_INT,
                'values' => [5, 6.5],
                'exception' => InvalidArgumentException::class
            ],
            [
                'type' => Cassandra::TYPE_VARCHAR,
                'values' => [5, '6'],
                'exception' => InvalidArgumentException::class
            ],
        ];
    }

}
