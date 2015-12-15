<?php

namespace app\core;

class View
{
    const VIEW_DIR = VIEW_DIR;

    public $title;
    public $containerClass = 'container';

    public function render($view, $params = [])
    {
        $filename = str_replace('/', DIRECTORY_SEPARATOR, $view);
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        require (self::VIEW_DIR . $filename . '.php');
        return ob_get_clean();
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setContainerClass($containerClass)
    {
        $this->containerClass = $containerClass;
    }
}