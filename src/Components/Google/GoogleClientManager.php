<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 21:09
 */

namespace Aigisu\Components\Google;


use Aigisu\Components\Configure\Configurable;
use Aigisu\Components\TokenSack;
use InvalidArgumentException;

class GoogleClientManager extends Configurable
{
    const TOKEN_NAME = 'google_token';

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

    public function setAccess()
    {
        if (!isset($this->config['access-type'])) {
            throw new InvalidArgumentException('Missing access access-type');
        }

        $this->client->setAccessType($this->config['access-type']);

        if (!$token = $this->getTokenSack()->getToken(self::TOKEN_NAME)) {
            throw new \RuntimeException('You need to create token via cli or http');
        } else {
            $this->client->setAccessToken($token);
            if ($this->refreshAccessToken()) {
                $this->saveAccessToken();
            }
        }
    }

    /**
     * Save current access token into token sack
     *
     * @throws \Exception
     */
    public function saveAccessToken()
    {
        $this->getTokenSack()->saveToken(self::TOKEN_NAME, json_encode($this->client->getAccessToken()));
    }

    /**
     * @return TokenSack
     */
    private function getTokenSack() : TokenSack
    {
        return $this->config[TokenSack::class];
    }

    /**
     * @param $refreshToken
     */
    private function addRefreshToken($refreshToken)
    {
        if (!$refreshToken) {
            return;
        }

        $this->client->fetchAccessTokenWithRefreshToken();
        $this->client->setAccessToken(array_merge(
                ['refresh_token' => $refreshToken],
                $this->client->getAccessToken())
        );
    }

    /**
     * @return bool
     */
    private function refreshAccessToken() : bool
    {
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->isUsingApplicationDefaultCredentials()) {
                $this->client->fetchAccessTokenWithAssertion();
            } else {
                $this->addRefreshToken($this->client->getRefreshToken());
            }
            return true;
        }
        return false;
    }

}
