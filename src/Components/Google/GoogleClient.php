<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 12:38
 */

namespace Aigisu\Components\Google;

use Google\Auth\Cache\InvalidArgumentException;

class GoogleClient extends \Google_Client
{
    /**
     * @param string|array $config
     */
    public function setAuthConfig($config)
    {
        if (!is_string($config) && !is_array($config)) {
            throw new InvalidArgumentException(
                sprintf('Invalid type of AuthConfig, must be and filename or array. %s given', gettype($config))
            );
        }
        parent::setAuthConfig($config);
    }
}
