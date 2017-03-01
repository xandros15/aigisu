<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-19
 * Time: 16:31
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Flash;
use Aigisu\Core\ActiveContainer;
use Interop\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Views\Twig;

abstract class AbstractController extends ActiveContainer
{

    const HOME_PATH_NAME = 'web.home';

    /** @var Flash */
    protected $flash;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->flash = new Flash($this->get(Messages::class));
    }


    /**
     * @param Response $response
     * @return Response
     */
    public function goHome(Response $response) : Response
    {
        $path = $this->get('router')->pathFor(self::HOME_PATH_NAME);
        return $response->withRedirect($path);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @param string $template
     * @param array $params
     * @return Response
     */
    protected function render(Request $request, Response $response, string $template, array $params = []) : Response
    {
        return $this->get(Twig::class)->render($response, $template, array_merge($params, [
            'is_guest' => $request->getAttribute('is_guest', true),
            'user' => $request->getAttribute('user', []),
        ]));
    }

}
