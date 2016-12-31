<?php

namespace Tapalava\Cassandra;

use Cassandra\Collection;
use Cassandra\Exception\InvalidArgumentException;
use Cassandra\Type;
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
    public function testValidArguments(Type $type, array $values, $expectedCount)
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
     * Attempting to create collections with illegal arguments produces exceptions.
     *
     * @test
     * @dataProvider invalidArguments
     */
    public function testInvalidArguments(Type $type, array $values, $exception)
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
                'type' => Type::int(),
                'values' => [6, 5, 2],
                'expectedCount' => 3
            ],
            [
                'type' => Type::varchar(),
                'values' => ['foo'],
                'expectedCount' => 1
            ],
            [
                'type' => Type::double(),
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
                'type' => Type::int(),
                'values' => [5, 6.5],
                'exception' => InvalidArgumentException::class
            ],
            [
                'type' => Type::varchar(),
                'values' => [5, '6'],
                'exception' => InvalidArgumentException::class
            ],
        ];
    }

}
