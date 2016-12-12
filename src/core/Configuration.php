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
    const DIR_SOURCE = __DIR__ . '/../../src/';
    const DIR_CONFIG = __DIR__ . '/../config/';
    const DIR_VIEW = __DIR__ . '/../common/view/';
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
        $this->applyParams(require self::DIR_CONFIG . 'params.php');
        /** @noinspection PhpIncludeInspection */
        $this->applyParams(require self::DIR_CONFIG . 'dependencies.php');
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
