<?php

namespace app\core;

use Slim\Collection;

final class Configuration extends Collection
{
    const WEB_BASENAME = 'web.php';

    public function __construct(array $items = [])
    {
        $this->defineDirs();
        parent::__construct(array_merge($this->getWebConfig(), $this->getDBConfig(), $items));
    }

    public function all()
    {
        return ['configuration' => parent::all()];
    }

    private function defineDirs()
    {
        defined('SOURCE_DIR') || define('SOURCE_DIR', ROOT_DIR . 'src' . DIRECTORY_SEPARATOR);
        defined('CONFIG_DIR') || define('CONFIG_DIR', SOURCE_DIR . 'config' . DIRECTORY_SEPARATOR);
        defined('VIEW_DIR') || define('VIEW_DIR', SOURCE_DIR . 'view' . DIRECTORY_SEPARATOR);
    }

    private function getWebConfig() : array
    {
        if (!is_file(CONFIG_DIR . self::WEB_BASENAME)) {
            throw new \RuntimeException("Can't find web configuration file. Searching in: " . CONFIG_DIR . self::WEB_BASENAME);
        }
        return require CONFIG_DIR . self::WEB_BASENAME;
    }

    private function getDBConfig() : array
    {
        return ['database' => require CONFIG_DIR . 'db.config.php'];
    }
}