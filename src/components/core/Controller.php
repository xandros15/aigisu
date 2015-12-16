<?php

namespace app\core;

use app\core\View;
use Slim\Container;

class Controller
{
    public $layout = 'layout/main';

    /** @var View */
    private $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function render($view, $params = [])
    {
        $content = $this->getView()->render($view, $params);

        if ($view === $this->layout) {
            return $this->getView()->render($view, $params);
        }
        return $this->getView()->render($this->layout, ['content' => $content]);
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
}