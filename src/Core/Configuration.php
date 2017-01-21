<?php

namespace Aigisu\Core;

use Slim\Container;

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
        /** @noinspection PhpIncludeInspection */
        $this->applyParams(require self::DIR_ROOT . '/config/params.php');
        /** @noinspection PhpIncludeInspection */
        $this->applyParams(require self::DIR_ROOT . '/config/dependencies.php');
        /** @noinspection PhpIncludeInspection */
        parent::__construct((array) (require self::DIR_ROOT . '/config/settings.php') + $items);
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
