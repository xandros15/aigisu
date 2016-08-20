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
        parent::__construct($items);
        $this->createWebConfig();
        $this->createDBConfig();
    }

    private function createWebConfig()
    {
        $this['siteUrl'] = rtrim($this->request->getUri()->withPath('')->withQuery('')->withFragment(''), '/');
        $this['locale'] = 'en';
    }

    private function createDBConfig()
    {
        /** @noinspection PhpIncludeInspection */
        $this['database'] = require self::DIR_CONFIG . 'db.config.php';
    }
}