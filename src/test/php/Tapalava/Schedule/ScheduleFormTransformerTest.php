<?php

namespace Tapalava\Schedule;

use PHPUnit_Framework_TestCase as TestCase;
use DateTime;

class ScheduleFormTransformerTest extends TestCase
{
    /**
     * Ensure that all properties are transformed to the model form correctly.
     *
     * @dataProvider validTransformations
     * @test
     */
    public function validFromView($viewData, Schedule $expectedObject)
    {
        $test = new ScheduleFormTransformer();

        $result = $test->fromView($viewData);

        $this->assertEquals($expectedObject->getId(), $result->getId());
        $this->assertEquals($expectedObject->getName(), $result->getName());
        $this->assertEquals($expectedObject->getDescription(), $result->getDescription());
        $this->assertEquals($expectedObject->getBanner(), $result->getBanner());
        $this->assertEquals($expectedObject->getLocation(), $result->getLocation());
        $this->assertEquals($expectedObject->getTags(), $result->getTags());
        $this->assertEquals($expectedObject->getDays(), $result->getDays());
    }

    /**
     * Ensure that all properties are transformed to view data correctly.
     *
     * @dataProvider validTransformations
     * @test
     */
    public function validFromModel($expectedViewData, Schedule $model)
    {
        $test = new ScheduleFormTransformer();

        $result = $test->toView($model);


        $this->assertEquals($expectedViewData['id'] ?? null, $result['id'] ?? null);
        $this->assertEquals($expectedViewData['name'] ?? null, $result['name'] ?? null);
        $this->assertEquals($expectedViewData['description'] ?? null, $result['description'] ?? null);
        $this->assertEquals($expectedViewData['banner'] ?? null, $result['banner'] ?? null);
        $this->assertEquals($expectedViewData['location'] ?? null, $result['location'] ?? null);
        $this->assertEquals($expectedViewData['tags'] ?? null, $result['tags'] ?? null);
        $this->assertEquals($expectedViewData['startDate'] ?? null, $result['startDate'] ?? null);
        $this->assertEquals($expectedViewData['endDate'] ?? null, $result['endDate'] ?? null);
    }

    /**
     * Ensure that properties can be transformed back and forth without loss.
     *
     * @dataProvider validTransformations
     * @test
     */
    public function bijectivity($viewData, Schedule $model)
    {
        $test = new ScheduleFormTransformer();

        $newModel = $test->fromView($viewData);
        $newView = $test->toView($newModel);

        $this->assertEquals($model->getId(), $newModel->getId());
        $this->assertEquals($model->getName(), $newModel->getName());
        $this->assertEquals($model->getDescription(), $newModel->getDescription());
        $this->assertEquals($model->getBanner(), $newModel->getBanner());
        $this->assertEquals($model->getLocation(), $newModel->getLocation());
        $this->assertEquals($model->getTags(), $newModel->getTags());
        $this->assertEquals($model->getDays(), $newModel->getDays());

        $this->assertEquals($viewData['id'] ?? null, $newView['id'] ?? null);
        $this->assertEquals($viewData['name'] ?? null, $newView['name'] ?? null);
        $this->assertEquals($viewData['description'] ?? null, $newView['description'] ?? null);
        $this->assertEquals($viewData['banner'] ?? null, $newView['banner'] ?? null);
        $this->assertEquals($viewData['location'] ?? null, $newView['location'] ?? null);
        $this->assertEquals($viewData['tags'] ?? null, $newView['tags'] ?? null);
        $this->assertEquals($viewData['startDate'] ?? null, $newView['startDate'] ?? null);
        $this->assertEquals($viewData['endDate'] ?? null, $newView['endDate'] ?? null);
    }

    public static function validTransformations()
    {
        return [
            [
                [
                    'id' => 'fake-id-001',
                    'name' => 'Fake Schedule',
                    'description' => 'This is a description',
                    'banner' => 'http://nope.city/.gif',
                    'location' => 'Mars',
                    'startDate' => '2016-12-15',
                    'endDate' => '2016-12-18',
                    'tags' => 'a,b,c',
                ],
                new Schedule(
                    'fake-id-001',
                    'Fake Schedule',
                    [
                        new DateTime('2016-12-15'),
                        new DateTime('2016-12-16'),
                        new DateTime('2016-12-17'),
                        new DateTime('2016-12-18'),
                    ],
                    'This is a description',
                    'http://nope.city/.gif',
                    'Mars',
                    ['a', 'b', 'c']
                ),
            ],
            [
                [],
                new Schedule(),
            ],
        ];
    }
}
