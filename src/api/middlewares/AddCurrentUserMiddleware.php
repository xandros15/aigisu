<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-21
 * Time: 23:35
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Api\Models\User;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Slim\Http\Request;
use Slim\Http\Response;

class AddCurrentUserMiddleware extends Middleware
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
            $request = $this->validateAuthenticate($request);
            $request = $this->addUserToRequest($request);
        } catch (OAuthServerException $exception) {
            //just catch and do nothing
        } catch (\Exception $exception) {
            return (new OAuthServerException($exception->getMessage(), 0, 'unknown_error',
                500))->generateHttpResponse($response);
            // @codeCoverageIgnoreEnd
        }
        return $next($request, $response);
    }


    /**
     * @param Request $request
     * @return Request
     */
    private function validateAuthenticate(Request $request) : Request
    {
        if ($request->hasHeader('Authorization')) {
            $server = $this->get(ResourceServer::class);
            $request = $server->validateAuthenticatedRequest($request);
        }

        return $request;
    }


    /**
     * @param Request $request
     * @return Request
     */
    private function addUserToRequest(Request $request) : Request
    {
        $user = null;
        $isGuest = true;

        if (($id = $request->getAttribute('oauth_client_id')) && ($user = User::find($id))) {
            $isGuest = false;
        }

        return $request->withAttributes(array_merge($request->getAttributes(), [
            'user' => $user,
            'is_guest' => $isGuest,
        ]));
    }

}
