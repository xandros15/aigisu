<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 21:09
 */

namespace Aigisu\Components\Google;


use Aigisu\Components\Configure\Configurable;

class GoogleClientManager extends Configurable
{

    /** @var  GoogleClient */
    private $client;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->createClient($config);
    }

    /**
     * @param $config
     * @return GoogleClient
     */
    public function createClient($config = []) : GoogleClient
    {
        $this->client = new GoogleClient($config);
        $this->setScopes();
        $this->createAuth();

        return $this->client;
    }

    /**
     * @param array $scopes
     */
    public function setScopes(array $scopes = [])
    {
        if ($scopes) {
            $this->config['scopes'] = $scopes;
        }

        $this->client->setScopes($this->config['scopes']);
    }

    /**
     * @see https://console.developers.google.com/apis/credentials
     * @param string|array $auth
     */
    public function createAuth($auth = null)
    {
        if ($auth) {
            $this->config['auth'] = $auth;
        }

        $this->client->setAuthConfig($this->config['auth']);
    }

    /**
     * @return GoogleClient
     */
    public function getClient() : GoogleClient
    {
        return $this->client;
    }

    /**
     * @param array $access
     */
    public function getAccess($access = [])
    {
        if ($access) {
            $this->config['access-file'] = $access['file'];
            $this->config['access-type'] = $access['type'];
        }

        $this->client->setAccessConfig($this->config);
    }

    /**
     * Save current access token into file
     * if filename is empty, getting filename from config
     * Remember to use full path eg. use __DIR__
     *
     * @param string $filename
     * @return bool
     * @throws \Exception
     */
    public function saveAccessToken(string $filename = '')
    {
        if ($filename) {
            $this->config['access-file'] = $filename;
        }

        return $this->client->saveAccessToken($this->config['access-file']);
    }

}
