<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-09
 * Time: 00:08
 */

namespace Aigisu\Components\Auth;


use Aigisu\Api\Middlewares\Middleware;
use Aigisu\Components\Http\UnauthorizedException;
use Aigisu\Models\User;
use InvalidArgumentException;
use Lcobucci\JWT\Parser;
use Slim\Http\Request;
use Slim\Http\Response;

class JWTAuthMiddleware extends Middleware
{
    const HEADER = 'Authorization';
    const TYPE = 'Bearer';

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws UnauthorizedException
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if ($request->hasHeader(self::HEADER)) {
            try {
                $request = $this->authorizeRequest($request);
            } catch (InvalidArgumentException $e) {
                throw new UnauthorizedException($request, $response);
            }
        }

        return $next($request, $response);
    }

    /**
     * @param Request $request
     * @return Request
     * @throws InvalidTokenException
     * @throws InvalidUserIdException
     */
    private function authorizeRequest(Request $request) : Request
    {
        $authHeader = $request->getHeaderLine(self::HEADER);

        list($type, $token) = explode(' ', $authHeader);
        if ($type == self::TYPE) {
            $auth = new JWTAuth($this->get('auth'));
            $token = (new Parser())->parse((string)$token); //throws InvalidArgumentException

            if (!$auth->verifyToken($token)) {
                throw new InvalidTokenException("Verify token fails");
            }

            if (!$user = User::find($token->getHeader('jti'))) {
                throw new InvalidUserIdException("Owner token not found");
            }

            $request = $request->withAttribute('user', $user)->withAttribute('is_guest', false);
        }

        return $request;
    }
}
