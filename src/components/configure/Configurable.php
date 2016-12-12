<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 21:14
 */

namespace Aigisu\Components\Configure;


use InvalidArgumentException;

abstract class Configurable
{
    /** @var  Config */
    protected $config;

    /**
     * GoogleDriveFilesystem constructor.
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->setConfig($config);
    }

    /**
     * @param array $config
     * @throws InvalidArgumentException
     */
    public function setConfig($config)
    {
        $this->config = is_array($config) ? new Config($config) : $config;

        if (!$this->config instanceof Config) {
            throw new InvalidArgumentException(
                sprintf('Wrong config type. Config must be array or %s instance', Config::class)
            );
        }
    }
}