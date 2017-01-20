<?php

namespace Aigisu\Core;

use Slim\Container;

/**
 * @property string locale
 * @property array database
 * @property array controllers
 */
final class Configuration extends Container
{
    const DIR_WEB = __DIR__ . '/../../web/';
    const DIR_ROOT = __DIR__ . '/../../';

    /**
     * Configuration constructor.
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);
        /** @noinspection PhpIncludeInspection */
        $this->applyParams(require self::DIR_ROOT . '/config/params.php');
        /** @noinspection PhpIncludeInspection */
        $this->applyParams(require self::DIR_ROOT . '/config/dependencies.php');
    }

    /**
     * @param array $params
     */
    private function applyParams(array $params)
    {
        foreach ($params as $param => $item) {
            $this[$param] = $item;
        }
    }
}
