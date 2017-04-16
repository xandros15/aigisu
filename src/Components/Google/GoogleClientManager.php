<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-30
 * Time: 21:09
 */

namespace Aigisu\Components\Google;

use Aigisu\Components\TokenSack;

class GoogleClientManager
{
    const TOKEN_NAME = 'google_token';

    /** @var TokenSack */
    private $tokenSack;
    /** @var  GoogleClient */
    private $client;

    public function __construct(TokenSack $tokenSack, $config = [])
    {
        $this->client = new GoogleClient($config);
        $this->client->setAuthConfig($config['auth']);
        $this->client->setScopes($config['scopes']);

        $this->tokenSack = $tokenSack;
    }

    /**
     * @return GoogleClient
     */
    public function getClient(): GoogleClient
    {
        return $this->client;
    }

    /**
     * @param string $type
     */
    public function setAccess($type = 'offline')
    {
        $this->client->setAccessType($type);

        if (!$token = $this->tokenSack->getToken(self::TOKEN_NAME)) {
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
        $this->tokenSack->saveToken(self::TOKEN_NAME, json_encode($this->client->getAccessToken()));
    }

    /**
     * @param $refreshToken
     */
    private function setRefreshToken($refreshToken)
    {
        if (!$refreshToken) {
            return;
        }

        $this->client->fetchAccessTokenWithRefreshToken();
        $this->client->setAccessToken(array_merge(['refresh_token' => $refreshToken], $this->client->getAccessToken()));
    }

    /**
     * @return bool
     */
    private function refreshAccessToken(): bool
    {
        if ($this->client->isAccessTokenExpired()) {
            if ($this->client->isUsingApplicationDefaultCredentials()) {
                $this->client->fetchAccessTokenWithAssertion();
            } else {
                $this->setRefreshToken($this->client->getRefreshToken());
            }

            return true;
        }

        return false;
    }

}
