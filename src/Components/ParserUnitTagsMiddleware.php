<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-23
 * Time: 02:09
 */

namespace Aigisu\Components;


use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class ParserUnitTagsMiddleware implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $tags = $request->getParam('tags');
        $parsedBody = $request->getParsedBody();

        if ($tags !== null) {
            $parsedBody['tags'] = $this->parseTags($tags);
        }

        return $next($request->withParsedBody($parsedBody), $response);
    }

    /**
     * @param $tags
     *
     * @return array
     */
    private function parseTags($tags): array
    {
        if (is_string($tags)) {
            $tags = $this->tagsToArray($tags);
        } elseif (!is_array($tags)) {
            $tags = [];
        }

        return array_filter(array_unique($tags));
    }

    /**
     * @param string $tagsString
     *
     * @return array
     */
    private function tagsToArray(string $tagsString): array
    {
        $tags = explode(',', $tagsString);
        $tags = array_map(function ($tag) {
            $tag = trim($tag);
            $tag = strtolower($tag);
            $tag = str_replace(' ', '_', $tag);

            return $tag;
        }, $tags);

        return $tags;
    }
}
