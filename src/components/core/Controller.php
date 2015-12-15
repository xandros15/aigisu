<?php

namespace app\core;

use app\core\View;

class Controller
{
    /** @var View */
    private $view;

    public function __construct()
    {
        $this->view = new View();
    }

    public function render($view, $params = [])
    {
        return $this->view->render($view, $params);
    }
}