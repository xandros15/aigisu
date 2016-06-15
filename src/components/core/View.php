<?php

namespace app\core;

class View
{
    protected $customsMethods = [];
    protected $attributes = [];
    /** @var string */
    protected $path;

    public function __construct(string $path = VIEW_DIR, array $customCallbacks = [])
    {
        $this->path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
        $this->addCustomsCallback($customCallbacks);
    }

    public function __get($name)
    {
        if (isset($this->attributes[$name])) {
            return $this->attributes[$name];
        }
    }

    public function __set($name, $value)
    {
        return $this->attributes[$name] = $value;
    }


    public function __call($name, $arguments)
    {
        if (!isset($this->customsMethods[$name])) {
            throw new \BadMethodCallException();
        }
        return call_user_func_array($this->customsMethods[$name], $arguments);
    }

    public function render($view, $params = [])
    {
        ob_start();
        ob_implicit_flush(false);
        extract(array_merge($this->attributes, $params), EXTR_OVERWRITE);
        include $this->getTemplateName($view);
        return ob_get_clean();
    }

    public function getRouter()
    {
        return $this->router;
    }

    protected function getTemplateName($name)
    {
        $name = $this->path . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $name) . '.php';
        if (!is_file($name) || !is_readable($name)) {
            throw new \RuntimeException("View cannot render `{$name}` because the template does not exist");
        }
        return $name;
    }

    protected function addCustomsCallback(array $callbacks)
    {
        foreach ($callbacks as $name => $callback) {
            if (!is_callable($callback)) {
                throw new \InvalidArgumentException("Method `$name` isn't callable");
            }
            $this->customsMethods[$name] = $callback;
        }
    }
}