<?php

namespace app\core;

use app\core\View\View;
use Slim\Container;
use Slim\Http\Response;

/**
 * @property Response response
 * @property Response request
 * @property View view
 */
class Controller
{
    public $layout = 'layout/main';

    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __get($name)
    {
        if($this->container->has($name)){
            return $this->container->get($name);
        }
        throw new \InvalidArgumentException();
    }

    public function render($view, $params = [])
    {
        $content = $this->view->render($view, $params);
        $render = ($view === $this->layout) ? $content : $this->view->render($this->layout,
            ['content' => $content]);

        return $this->response->write($render);
    }

    public function renderAjax($view, $params = [])
    {
        $render = $this->view->render($view, $params);

        return $this->response->write($render);
    }

    public function goHome()
    {
        return $this->response->withRedirect('/', 301);
    }

    public function goBack()
    {
        if ($this->request->hasHeader('HTTP_REFERER')) {
            $referer = $this->request->getHeader('HTTP_REFERER')[0];
            return $this->response->withRedirect($referer, 301);
        }
        return $this->goHome();
    }
}