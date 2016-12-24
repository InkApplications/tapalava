<?php

namespace Tapalava\Event;

use PHPUnit_Framework_TestCase as TestCase;
use DateTime;

class EventFormTransformerTest extends TestCase
{
    /**
     * Ensure that all properties are transformed to the model form correctly.
     *
     * @dataProvider validTransformations
     * @test
     */
    public function validFromView($viewData, Event $expectedObject, $scheduleId)
    {
        $test = new EventFormTransformer();

        $result = $test->fromView($viewData, $scheduleId);

        $this->assertEquals($expectedObject->getId(), $result->getId());
        $this->assertEquals($scheduleId, $result->getScheduleId());
        $this->assertEquals($expectedObject->getName(), $result->getName());
        $this->assertEquals($expectedObject->getStart(), $result->getStart());
        $this->assertEquals($expectedObject->getEnd(), $result->getEnd());
        $this->assertEquals($expectedObject->getCategory(), $result->getCategory());
        $this->assertEquals($expectedObject->getTags(), $result->getTags());
        $this->assertEquals($expectedObject->getRoom(), $result->getRoom());
        $this->assertEquals($expectedObject->getHosts(), $result->getHosts());
        $this->assertEquals($expectedObject->getDescription(), $result->getDescription());
        $this->assertEquals($expectedObject->getBanner(), $result->getBanner());
    }

    /**
     * Ensure that all properties are transformed to view data correctly.
     *
     * @dataProvider validTransformations
     * @test
     */
    public function validFromModel($expectedViewData, Event $model)
    {
        $test = new EventFormTransformer();

        $result = $test->toView($model);

        $this->assertEquals($expectedViewData['id'] ?? null, $result['id'] ?? null);
        $this->assertEquals($expectedViewData['scheduleId'] ?? null, $result['scheduleId'] ?? null);
        $this->assertEquals($expectedViewData['name'] ?? null, $result['name'] ?? null);
        $this->assertEquals($expectedViewData['start'] ?? null, $result['start'] ?? null);
        $this->assertEquals($expectedViewData['end'] ?? null, $result['end'] ?? null);
        $this->assertEquals($expectedViewData['category'] ?? null, $result['category'] ?? null);
        $this->assertEquals($expectedViewData['tags'] ?? null, $result['tags'] ?? null);
        $this->assertEquals($expectedViewData['room'] ?? null, $result['room'] ?? null);
        $this->assertEquals($expectedViewData['hosts'] ?? null, $result['hosts'] ?? null);
        $this->assertEquals($expectedViewData['description'] ?? null, $result['description'] ?? null);
        $this->assertEquals($expectedViewData['banner'] ?? null, $result['banner'] ?? null);
    }

    /**
     * Ensure that properties can be transformed back and forth without loss.
     *
     * @dataProvider validTransformations
     * @test
     */
    public function bijectivity($viewData, Event $model)
    {
        $test = new EventFormTransformer();

        $newModel = $test->fromView($viewData, $viewData['scheduleId'] ?? null);
        $newView = $test->toView($newModel);

        $this->assertEquals($model->getId(), $newModel->getId());
        $this->assertEquals($model->getScheduleId(), $newModel->getScheduleId());
        $this->assertEquals($model->getName(), $newModel->getName());
        $this->assertEquals($model->getStart(), $newModel->getStart());
        $this->assertEquals($model->getEnd(), $newModel->getEnd());
        $this->assertEquals($model->getCategory(), $newModel->getCategory());
        $this->assertEquals($model->getTags(), $newModel->getTags());
        $this->assertEquals($model->getRoom(), $newModel->getRoom());
        $this->assertEquals($model->getHosts(), $newModel->getHosts());
        $this->assertEquals($model->getDescription(), $newModel->getDescription());
        $this->assertEquals($model->getBanner(), $newModel->getBanner());

        $this->assertEquals($viewData['id'] ?? null, $newView['id'] ?? null);
        $this->assertEquals($viewData['scheduleId'] ?? null, $newView['scheduleId'] ?? null);
        $this->assertEquals($viewData['name'] ?? null, $newView['name'] ?? null);
        $this->assertEquals($viewData['start'] ?? null, $newView['start'] ?? null);
        $this->assertEquals($viewData['end'] ?? null, $newView['end'] ?? null);
        $this->assertEquals($viewData['category'] ?? null, $newView['category'] ?? null);
        $this->assertEquals($viewData['tags'] ?? null, $newView['tags'] ?? null);
        $this->assertEquals($viewData['room'] ?? null, $newView['room'] ?? null);
        $this->assertEquals($viewData['hosts'] ?? null, $newView['hosts'] ?? null);
        $this->assertEquals($viewData['description'] ?? null, $newView['description'] ?? null);
        $this->assertEquals($viewData['banner'] ?? null, $newView['banner'] ?? null);
    }

    public static function validTransformations()
    {
        return [
            [
                [
                    'id' => 'fake-event-001',
                    'scheduleId' => 'fake-schedule-id-001',
                    'name' => 'Fake Event',
                    'start' => '2016-12-15T06:00:00+05:00',
                    'end' => '2016-12-15T08:00:00+05:00',
                    'category' => 'Fake Category',
                    'tags' => 'a,b,c',
                    'room' => 'Fake Room',
                    'hosts' => 'john,jane',
                    'description' => 'Fake Description',
                    'banner' => 'http://nope.city/.gif',
                ],
                new Event(
                    'fake-event-001',
                    'fake-schedule-id-001',
                    'Fake Event',
                    new DateTime('2016-12-15T06:00:00+05:00'),
                    new DateTime('2016-12-15T08:00:00+05:00'),
                    'Fake Category',
                    ['a', 'b', 'c'],
                    'Fake Room',
                    ['john', 'jane'],
                    'Fake Description',
                    'http://nope.city/.gif'
                ),
                'fake-schedule-id-lookup',
            ],
            [
                [],
                new Event(),
                null,
            ],
        ];
    }
}
