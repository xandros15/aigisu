<?php

namespace Aigisu;

use Slim\Container;

/**
 * @property string locale
 * @property array database
 * @property array controllers
 */
final class Configuration extends Container
{
    const DIR_SOURCE = __DIR__ . '/../../src/';
    const DIR_CONFIG = __DIR__ . '/../../config/';
    const DIR_VIEW = __DIR__ . '/../../view/';

    public function __construct(array $items = [])
    {
        $default = $this->getWebConfig();
        $default['database'] = $this->getDBConfig();
        parent::__construct(array_merge($default, $items));
    }

    private function getWebConfig() : array
    {
        /** @noinspection PhpIncludeInspection */
        return require self::DIR_CONFIG . 'web.php';
    }

    private function getDBConfig() : array
    {
        /** @noinspection PhpIncludeInspection */
        return require self::DIR_CONFIG . 'db.config.php';
    }
}