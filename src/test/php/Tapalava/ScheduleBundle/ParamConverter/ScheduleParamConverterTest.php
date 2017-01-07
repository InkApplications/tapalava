<?php

namespace Tapalava\ScheduleBundle\ParamConverter;

use PHPUnit_Framework_TestCase as TestCase;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Tapalava\Schedule\FakeScheduleRepository;
use Tapalava\Schedule\Schedule;

class ScheduleParamConverterTest extends TestCase
{
    /**
     * Param Converter only supports the Schedule class.
     *
     * @dataProvider supportedClasses
     * @test
     */
    public function supportsOnlySchedule($class, $expected)
    {
        $converter = new ScheduleParamConverter(new FakeScheduleRepository());
        $configuration = new ParamConverter([]);
        $configuration->setClass($class);

        $result = $converter->supports($configuration);

        $this->assertEquals($expected, $result);
    }

    /** List of supported and non-supported classes to test. */
    public static function supportedClasses(): array
    {
        return [
            [
                'class' => null,
                'expected' => false,
            ],
            [
                'class' => \stdClass::class,
                'expected' => false,
            ],
            [
                'class' => Schedule::class,
                'expected' => true,
            ],
        ];
    }

    /**
     * Parameter is used to lookup a schedule ID and be set to the attribute bag.
     *
     * @test
     */
    public function successfulConversion()
    {
        $converter = new ScheduleParamConverter(new FakeScheduleRepository());
        $configuration = new ParamConverter([]);
        $configuration->setClass(Schedule::class);
        $configuration->setName('test-id-param');
        $request = new class extends Request {
            public function get($key, $default = null) {
                return 'fake-id-001';
            }
        };

        $converter->apply($request, $configuration);
        $resultParameter = $request->attributes->get('test-id-param');

        $this->assertNotNull($resultParameter);
        $this->assertInstanceOf(Schedule::class, $resultParameter);
        $this->assertEquals('fake-id-001', $resultParameter->getId());
    }

    /**
     * If the ID provided by the parameter results cannot be found in the
     * repository, a not found exception is thrown.
     *
     * @test
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function unsuccessfulConversion()
    {
        $converter = new ScheduleParamConverter(new FakeScheduleRepository());
        $configuration = new ParamConverter([]);
        $configuration->setClass(Schedule::class);
        $configuration->setName('test-id-param');
        $request = new class extends Request {
            public function get($key, $default = null) {
                return 'missing-id';
            }
        };

        $converter->apply($request, $configuration);
    }
}
