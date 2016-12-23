<?php

namespace Tapalava\Http;

use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\HttpFoundation\Request;

class HttpParserTest extends TestCase
{
    /**
     * Ensure that HTTP forms will be decoded into arrays properly.
     *
     * @test
     */
    public function testHttpForm()
    {
        $parser = new RequestParser();
        $request = new Request();
        $request->setRequestFormat('html');
        $request->request->set('test', ['foo' => 'bar', 'baz' => 'qux']);

        $result = $parser->getEntityFromPost($request, 'test');

        $this->assertEquals('bar', $result['foo']);
        $this->assertEquals('qux', $result['baz']);
    }

    /**
     * Parser should throw an HTTP exception if the key is missing/doesn't match.
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @test
     */
    public function testHttpFormMissingEntity()
    {
        $parser = new RequestParser();
        $request = new Request();
        $request->setRequestFormat('html');
        $request->request->set('missing', ['foo' => 'bar', 'baz' => 'qux']);

        $parser->getEntityFromPost($request, 'test');
    }

    /**
     * Ensure that JSON Content is deserialized into a proper array.
     *
     * @test
     */
    public function testJsonContent()
    {
        $parser = new RequestParser();
        $content = '{"test": { "foo": "bar", "baz": "qux" }}';
        $request = new Request([], [], [], [], [], [], $content);
        $request->setRequestFormat('json');

        $result = $parser->getEntityFromPost($request, 'test');

        $this->assertEquals('bar', $result['foo']);
        $this->assertEquals('qux', $result['baz']);
    }

    /**
     * Parser should throw an HTTP exception invalid JSON is provided
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @test
     */
    public function testJsonContentInvalid()
    {
        $parser = new RequestParser();
        $content = 'This is not JSON!';
        $request = new Request([], [], [], [], [], [], $content);
        $request->setRequestFormat('json');

        $parser->getEntityFromPost($request, 'test');
    }

    /**
     * Parser should throw an HTTP exception if the key is missing/doesn't match.
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @test
     */
    public function testJsonContentMissingEntity()
    {
        $parser = new RequestParser();
        $content = '{"missing": { "foo": "bar", "baz": "qux" }}';
        $request = new Request([], [], [], [], [], [], $content);
        $request->setRequestFormat('json');

        $parser->getEntityFromPost($request, 'test');
    }

    /**
     * Parser should throw an error if the request format doesn't match anything known.
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @test
     */
    public function testUnknownFormat()
    {
        $parser = new RequestParser();
        $request = new Request();
        $request->setRequestFormat('foo');

        $parser->getEntityFromPost($request, 'test');
    }
}
