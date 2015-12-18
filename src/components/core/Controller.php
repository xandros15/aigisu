<?php

namespace app\core;

use Main;
use app\core\View;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;

class Controller
{
    public $layout = 'layout/main';

    /** @var Request */
    public $request;

    /** @var Response */
    public $response;

    /** @var View */
    private $view;

    public function __construct(Container $container)
    {
        $this->request      = Main::$app->request = $container->request;
        $this->response     = $container->response;
        $this->view         = new View();
    }

    public function render($view, $params = [])
    {
        $content = $this->getView()->render($view, $params);
        $render  = ($view === $this->layout) ? $content : $this->getView()->render($this->layout,
                ['content' => $content]);

        return $this->response->write($render);
    }

    public function getView()
    {
        if ($this->view === null) {
            $this->view = new View();
        }

        return $this->view;
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
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