<?php

namespace Aigisu\Common\Controllers;

use Aigisu\Common\Components\View\View;
use Aigisu\Core\ActiveContainer;

/**
 * Class Controller
 * @package Aigisu\Common\Controllers
 *
 * @property View $view
 */
abstract class Controller extends ActiveContainer
{
    public $layout = 'layout/main';

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
        return $this->response->withRedirect($this->siteUrl, 301);
    }
}