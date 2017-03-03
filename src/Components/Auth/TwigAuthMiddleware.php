<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 17:56
 */

namespace Aigisu\Components\Auth;


use Aigisu\Core\ActiveContainer;
use Aigisu\Core\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

class TwigAuthMiddleware extends ActiveContainer implements MiddlewareInterface
{

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next): Response
    {
        $this->replaceTwigCallback($request);
        return $next($request, $response);
    }

    private function replaceTwigCallback(ServerRequestInterface $request)
    {
        /** @var $twig Twig */
        $twig = $this->get(Twig::class);
        $twig->getEnvironment()->addGlobal('is_guest', $request->getAttribute('is_guest', true));
        $twig->getEnvironment()->addGlobal('user', $request->getAttribute('user', []));
    }
}
