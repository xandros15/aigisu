<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 17:56
 */

namespace Aigisu\Web\Components\Auth;


use Aigisu\Components\Api\Api;
use Aigisu\Core\ActiveContainer;
use Aigisu\Core\MiddlewareInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class TwigAuthMiddleware extends ActiveContainer implements MiddlewareInterface
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
        $this->addIdentToEnvironment();

        return $next($request, $response);
    }

    private function addIdentToEnvironment()
    {
        $this->get(Twig::class)
             ->getEnvironment()
             ->addGlobal('ident', new Ident($this->get(Api::class), new JWTAuth()));
    }
}
