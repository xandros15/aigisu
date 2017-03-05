<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-05
 * Time: 01:09
 */

namespace Aigisu\Middlewares;


use Aigisu\Components\Http\Exceptions\UnauthorizedException;
use Aigisu\Core\ActiveContainer;
use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class BedroomLockMiddleware extends ActiveContainer implements MiddlewareInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws UnauthorizedException
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $auth = $this->get('bedroom.lock');
        $login = $request->getServerParam('PHP_AUTH_USER');
        $password = $request->getServerParam('PHP_AUTH_PW');
        if ($login != $auth['login'] || $password != $auth['pass']) {
            $response = $response->withHeader('WWW-Authenticate', 'Basic realm="My Realm"');
            throw new UnauthorizedException($request, $response);
        }

        return $next($request, $response);
    }
}