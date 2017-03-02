<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-11-19
 * Time: 16:31
 */

namespace Aigisu\Web\Controllers;


use Aigisu\Components\Auth\RequestAdapter;
use Aigisu\Components\Flash;
use Aigisu\Core\ActiveContainer;
use Interop\Container\ContainerInterface;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Interfaces\CollectionInterface;
use Slim\Views\Twig;

abstract class AbstractController extends ActiveContainer
{

    const HOME_PATH_NAME = 'web.home';

    /** @var Flash */
    protected $flash;

    /**
     * AbstractController constructor.
     * @param ContainerInterface $container
     */
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
     * @param array|CollectionInterface $params
     * @return Response
     */
    protected function render(Request $request, Response $response, string $template, $params = []) : Response
    {
        $params = (array)($params instanceof CollectionInterface ? $params->all() : $params);
        $params = array_merge($this->authParams($request), $params);
        return $this->get(Twig::class)->render($response, $template, $params);
    }

    /**
     * @param Request $request
     * @return array
     */
    private function authParams(Request $request) : array
    {
        $adapter = new RequestAdapter($request);
        return $adapter->all();
    }

}
