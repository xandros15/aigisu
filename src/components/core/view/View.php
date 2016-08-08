<?php

namespace Aigisu\View;

use BadMethodCallException;
use Slim\Container;

/**
 * @property string title
 * @property string containerClass
 */
class View
{
    /** @var string */
    protected $path;

    /** @var CallbackManager */
    private $callbackManager;

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/\\') . DIRECTORY_SEPARATOR;
        $this->callbackManager = new CallbackManager();
    }

    public function __invoke(Container $container)
    {
        $urlExtension = new UrlExtension($container);
        $callbackManager = new CallbackManager();
        $urlExtension->applyCallbacks($callbackManager);
        $this->addCallbackManager($callbackManager);

        return $this;
    }

    public function addCallbackManager(CallbackManager $callbackManager)
    {
        $this->callbackManager = $callbackManager;
    }

    public function __call($name, $arguments)
    {
        if (!($this->callbackManager instanceof CallbackManager)) {
            throw new BadMethodCallException("Mehod {$name} doesn't exist.");
        }

        return call_user_func_array([$this->callbackManager, $name], $arguments);
    }

    public function render($view, $params = [])
    {
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        /** @noinspection PhpIncludeInspection */
        include $this->getTemplateName($view);
        return ob_get_clean();
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