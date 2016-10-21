<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-21
 * Time: 16:51
 */

namespace Aigisu\Components\Oauth;


use Slim\Http\Request;
use Slim\Http\Response;

class ClientToUserMiddleware
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $request = $this->attachParams($request);
        return $next($request, $response);
    }

    /**
     * @param Request $request
     * @return Request
     */
    private function attachParams(Request $request) : Request
    {
        $clientId = $request->getParam('client_id');
        $username = $request->getParam('username');
        if ($clientId !== null && $username === null) {
            $request = $request->withParsedBody(array_merge($request->getParams(), [
                'username' => $clientId,
            ]));
        } elseif ($username !== null && $clientId === null) {
            $request = $request->withParsedBody(array_merge($request->getParams(), [
                'client_id' => $username,
            ]));
        }

        return $request;
    }
}