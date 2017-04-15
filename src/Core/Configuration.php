<?php

namespace Aigisu\Core;

use Slim\Container;

final class Configuration extends Container
{
    const DIR_WEB = __DIR__ . '/../../web/';
    const DIR_ROOT = __DIR__ . '/../../';

    /**
     * Configuration constructor.
     */
    public function __construct()
    {
        /** @noinspection PhpIncludeInspection */
        $this->applyParams(require self::DIR_ROOT . '/config/params.php');
        /** @noinspection PhpIncludeInspection */
        $this->applyParams(require self::DIR_ROOT . '/config/dependencies.php');
        /** @noinspection PhpIncludeInspection */
        parent::__construct(require self::DIR_ROOT . '/config/settings.php');
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
