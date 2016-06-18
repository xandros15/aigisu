<?php

namespace app\core\View;

class View
{
    protected $attributes = [];
    /** @var string */
    protected $path;

    /** @var ViewExtension */
    private $extensions = [];

    /** @var [] */
    private $callbacks = [];

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
        throw new \InvalidArgumentException();
    }

    public function __set($name, $value)
    {
        return $this->attributes[$name] = $value;
    }


    public function __call($name, $arguments)
    {
        if (!isset($this->callbacks[$name])) {
            throw new \BadMethodCallException();
        }
        return call_user_func_array($this->callbacks[$name], $arguments);
    }

    public function render($view, $params = [])
    {
        $this->initExtensions();
        ob_start();
        ob_implicit_flush(false);
        extract(array_merge($this->attributes, $params), EXTR_OVERWRITE);
        /** @noinspection PhpIncludeInspection */
        include $this->getTemplateName($view);
        return ob_get_clean();
    }

    public function addExtension(ViewExtension $extension)
    {
        $this->extensions[$extension->getName()] = $extension;
    }

    public function initExtensions()
    {
        foreach ($this->extensions as $extension) {
            /** @var $extension ViewExtension */
            $this->callbacks = array_merge($this->callbacks, $extension->getCallbacks());
        }
    }

    protected function getTemplateName($name)
    {
        $name = $this->path . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name) . '.php';
        if (!is_file($name) || !is_readable($name)) {
            throw new \RuntimeException("View cannot render `{$name}` because the template does not exist");
        }
        return $name;
    }
}