<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-05
 * Time: 18:52
 */

namespace Aigisu\Api\Middlewares\CG;


use Aigisu\Api\Middlewares\Middleware;
use Google_Service_Exception as GoogleServiceException;
use GuzzleHttp\Exception\BadResponseException;
use Slim\Http\Request;
use Slim\Http\Response;

class ExtendedServerExceptionHandler extends Middleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        try {
            $response = $next($request, $response);
        } catch (GoogleServiceException $exception) {
            $response = $response->withJson(json_decode($exception->getMessage(), true), $exception->getCode());
        } catch (BadResponseException $exception) {
            $response = $response->withJson(json_decode($exception->getMessage(), true), $exception->getCode());
        }

        return $response;
    }
}
