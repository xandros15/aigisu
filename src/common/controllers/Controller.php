<?php

namespace Aigisu\Common\Controllers;

use Aigisu\Common\Components\View\View;
use Aigisu\Core\ActiveContainer;
use GuzzleHttp\Client;
use Slim\Http\Response;

/**
 * Class Controller
 * @package Aigisu\Common\Controllers
 *
 * @property View $view
 */
abstract class Controller extends ActiveContainer
{
    const LAYOUT = 'layout/main';

    /**
     * @param Response $response
     * @param string $view
     * @param array $params
     * @return Response
     */
    public function render(Response $response, string $view, array $params = []) : Response
    {
        $content = $this->view->render($view, $params);

        if ($view !== self::LAYOUT) {
            $content = $this->view->render(self::LAYOUT, ['content' => $content]);
        }

        return $response->write($content);
    }

    /**
     * @param Response $response
     * @param $view
     * @param array $params
     * @return Response
     */
    public function renderAjax(Response $response, string $view, array $params = []) : Response
    {
        $render = $this->view->render($view, $params);

        return $response->write($render);
    }

    /**
     * @return Response
     */
    public function goBack() : Response
    {
        if ($this->request->hasHeader('HTTP_REFERER')) {
            $referer = $this->request->getHeader('HTTP_REFERER')[0];
            return $this->response->withRedirect($referer, 301);
        }
        return $this->goHome();
    }

    /**
     * @return Response
     */
    public function goHome() : Response
    {
        return $this->response->withRedirect($this->siteUrl, 301);
    }

    protected function getClient() : Client
    {
        return new Client(['base_uri' => $this->siteUrl]);
    }
}