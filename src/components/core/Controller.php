<?php

namespace app\core;

use Main;
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
        $this->view = new View(Configuration::getInstance()->siteUrl);
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
            $this->view = new View(['basePath' => Configuration::getInstance()->siteUrl]);
        }

        return $this->view;
    }

    public function renderAjax($view, $params = [])
    {
        $render = $this->getView()->render($view, $params);

        return $this->response->write($render);
    }

    public function getLayout()
    {
        return $this->layout;
    }

    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    public function goBack()
    {
        if ($this->request->hasHeader('HTTP_REFERER')) {
            $referer = $this->request->getHeader('HTTP_REFERER')[0];
            return $this->response->withRedirect($referer, 301);
        }
        return $this->goHome();
    }

    public function goHome()
    {
        return $this->response->withRedirect(Configuration::getInstance()->siteUrl, 301);
    }
}