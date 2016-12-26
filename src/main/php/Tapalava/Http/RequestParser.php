<?php

namespace Tapalava\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Extracts information out of various formats of HTTP Requests.
 *
 * @author Maxwell Vandervelde <Max@MaxVandervelde.com>
 */
class RequestParser
{
    /**
     * Get an entity array out of a POST request.
     *
     * This supports HTML form posts as well as JSON content entities.
     *
     * The data to be extracted from the request must be keyed in the request.
     * For example, to extract the 'widget' entity from a request, the JSON
     * content posted must be structured like this:
     *
     *     {
     *         "widget": {
     *             "foo": "bar",
     *             "baz": "qux"
     *         }
     *     }
     *
     * Or if it's a form-encoded request, the structure should look like
     *
     *     schedule[foo]: bar
     *     schedule[baz]: qux
     *
     * etc.
     * Both will result in the same output of:
     *
     *     [
     *         'foo' => 'bar',
     *         'baz' => 'qux'
     *     ]
     *
     * @param Request $request A POST request containing user-data to be extracted.
     * @param string $entity The data entity to be extracted from the request.
     * @throws BadRequestHttpException If the Request contains invalid or missing data.
     * @return array Key/value data of the entity posted by the user.
     */
    public function getEntityFromPost(Request $request, $entity): array
    {
        $format = $request->getRequestFormat('html');

        switch ($format) {
            case 'json':
                return $this->fromJson($request, $entity);
            case 'html':
                return $this->fromHtml($request, $entity);
        }

        throw new BadRequestHttpException('Invalid format requested: ' . $format);
    }

    /**
     * Get HTTP form entities from an HTTP Request.
     *
     * @param Request $request A POST request containing user-data to be extracted.
     * @param string $entity The data entity to be extracted from the request.
     * @return array Key/value data of the entity posted by the user.
     */
    private function fromHtml(Request $request, $entity): array
    {
        $data = $request->get($entity);

        if (null === $data) {
            throw new BadRequestHttpException('Missing required form entity: ' . $entity);
        }

        return $data;
    }

    /**
     * Deserialize JSON entities from posted data.
     *
     * @param Request $request A POST request containing user-data to be extracted.
     * @param string $entity The data entity to be extracted from the request.
     * @return array Key/value data of the entity posted by the user.
     */
    private function fromJson(Request $request, $entity): array
    {
        $content = $request->getContent();
        $json = json_decode($content, true);

        if (null === $json) {
            throw new BadRequestHttpException('JSON value could not be parsed from content');
        }

        if (false === isset($json[$entity])) {
            throw new BadRequestHttpException('missing JSON entity: ' . $entity);
        }

        return $json[$entity];
    }
}
