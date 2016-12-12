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

    /**
     * @param $config
     */
    public function setAccessConfig($config)
    {
        if (!isset($config['access-type'], $config['access-file'])) {
            throw new InvalidArgumentException('Missing access access-type or access-file');
        }

        $this->setAccessType($config['access-type']);

        if (!file_exists($config['access-file'])) {
            throw new \RuntimeException('You need to create token via cli or http');
        } else {
            $this->setAccessToken(file_get_contents($config['access-file']));
            if ($this->accessTokenIfExpired()) {
                $this->saveAccessToken($config['access-file']);
            }
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    private function accessTokenIfExpired() : bool
    {
        if ($this->isAccessTokenExpired()) {
            if ($this->isUsingApplicationDefaultCredentials()) {
                $this->fetchAccessTokenWithAssertion();
            } else {
                $refreshToken = $this->getRefreshToken();
                $this->fetchAccessTokenWithRefreshToken();
                $this->setAccessToken(array_merge(['refresh_token' => $refreshToken], $this->getAccessToken()));
            }
            return true;
        }
        return false;
    }

    /**
     * Saving Access token
     *
     * Remember to use full path eg. use __DIR__
     *
     * @param string $filename
     * @return bool
     * @throws \Exception
     */
    public function saveAccessToken(string $filename) : bool
    {
        $basename = basename($filename);
        $path = dirname($filename);
        if (!$path = realpath($path)) {
            $umask = umask(0);
            if (!@mkdir($path, 0755, true)) {
                throw new \Exception(sprintf('Impossible to create the root directory "%s".', $path));
            }
            umask($umask);
        }

        return (bool) file_put_contents($path . DIRECTORY_SEPARATOR . $basename,
            json_encode($this->getAccessToken()));
    }
}
