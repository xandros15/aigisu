<?php

namespace Aigisu\Core;

use Aigisu\Components\File\FileUploader;
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

    public function __construct(array $items = [])
    {
        parent::__construct($items);
        $this->createWebConfig();
        $this->createDBConfig();
    }

    private function createWebConfig()
    {
        $this['webDirectory'] = self::DIR_WEB;
        $this['uploadDirectory'] = self::DIR_WEB . DIRECTORY_SEPARATOR . 'upload';
        $this['siteUrl'] = rtrim($this->request->getUri()->withPath('')->withQuery('')->withFragment(''), '/');
        $this['locale'] = 'en';
        $this['FileUploader'] = new FileUploader($this->get('uploadDirectory'));
    }

    private function createDBConfig()
    {
        /** @noinspection PhpIncludeInspection */
        $this['database'] = require self::DIR_CONFIG . 'db.config.php';
    }
}