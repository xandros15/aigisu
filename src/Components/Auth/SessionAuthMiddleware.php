<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-23
 * Time: 02:00
 */

namespace Aigisu\Components\Auth;


use Aigisu\Core\MiddlewareInterface;
use Aigisu\Models\User;
use Slim\Http\Request;
use Slim\Http\Response;

class SessionAuthMiddleware implements MiddlewareInterface
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
        $request = $this->authorizeRequest($request);

        return $next($request, $response);
    }

    /**
     * @param Request $request
     *
     * @return Request
     * @throws InvalidTokenException
     */
    private function authorizeRequest(Request $request): Request
    {
        $auth = new SessionAuth();
        if (!$auth->isGuest()) {
            if (!$user = User::find($auth->getAuthId())) {
                throw new InvalidTokenException("Session owner not found");
            }

            $request = $request->withAttribute('user', $user)->withAttribute('is_guest', false);
        }

        return $request;
    }
}
