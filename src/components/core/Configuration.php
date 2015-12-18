<?php

namespace app\core;

use Exception;

class Configuration
{
    const WEB_BASENAME = 'web.php';

    /** @var Configuration */
    private static $instance;

    /** @var array */
    private $config = [];

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new Configuration();
            self::$instance->configurate();
        }
        return self::$instance;
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function get($name)
    {
        if (!isset($this->config[$name])) {
            trigger_error("Property {$name} no exists in " . self::class . ".", E_USER_WARNING);
            return null;
        }
        return $this->config[$name];
    }

    private function configurate()
    {
        $this->defineDirs();
        $this->loadWebConfiguration();
        $this->loadDatabaseConfiguration();
    }

    private function defineDirs()
    {
        defined('SOURCE_DIR') || define('SOURCE_DIR', ROOT_DIR . 'src' . DIRECTORY_SEPARATOR);
        defined('CONFIG_DIR') || define('CONFIG_DIR', SOURCE_DIR . 'config' . DIRECTORY_SEPARATOR);
        defined('VIEW_DIR') || define('VIEW_DIR', SOURCE_DIR . 'view' . DIRECTORY_SEPARATOR);
    }

    private function loadWebConfiguration()
    {
        if (!is_file(CONFIG_DIR . self::WEB_BASENAME)) {
            throw new Exception("Can't find web configuration file. Searching in: " . CONFIG_DIR . self::WEB_BASENAME);
        }
        $this->config = require_once CONFIG_DIR . self::WEB_BASENAME;
    }

    private function loadDatabaseConfiguration()
    {
        require CONFIG_DIR . 'db.config.php';
    }
}