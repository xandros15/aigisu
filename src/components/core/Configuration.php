<?php

namespace app\core;

use Slim\Collection;

final class Configuration extends Collection
{
    const DIR_SOURCE = __DIR__ . '/../../src/';
    const DIR_CONFIG = __DIR__ . '/../../config/';
    const DIR_VIEW = __DIR__ . '/../../view/';

    public function __construct(array $items = [])
    {
        parent::__construct(array_merge(
            $this->getWebConfig(),
            ['database' => $this->getDBConfig()],
            $items
        ));
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