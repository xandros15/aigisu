<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-18
 * Time: 19:34
 */

namespace Middlewares;


use Aigisu\Middleware;
use Slim\Http\Request;
use Slim\Http\Response;

class TrailingSearch extends Middleware
{
    const SEARCH_PARAM = 'q'; //@todo use param from search engine

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $searchParam = $request->getQueryParam(self::SEARCH_PARAM);

        if ($searchParam === '') {
            $query = $request->getQueryParams();
            unset($query[self::SEARCH_PARAM]);
            return $response->withRedirect($request->getUri()->withQuery($query ?: ''), 301);
        }

        return $next($request, $response);
    }
}