<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-03
 * Time: 02:54
 */

namespace Aigisu\Components\Imgur;


use AdamPaterson\OAuth2\Client\Provider\Imgur as ImgurProvider;
use Aigisu\Components\TokenSack;
use InvalidArgumentException;
use League\OAuth2\Client\Provider\AbstractProvider;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Client implements ClientInterface
{
    const TOKEN_NAME = 'imgur_token';

    const ENDPOINT = 'https://api.imgur.com/3/';

    /** @var \GuzzleHttp\Client */
    private $http;
    /** @var Imgur */
    private $authorization;
    /** @var array */
    private $token;
    /** @var array */
    private $authKeys;
    /** @var TokenSack */
    private $tokenSack;

    /**
     * Client constructor.
     *
     * @param TokenSack $tokenSack
     * @param array $keyring
     */
    public function __construct(TokenSack $tokenSack, array $keyring)
    {
        $this->tokenSack = $tokenSack;
        $this->setAuthKeys($keyring);
    }

    /**
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     */
    public function execute(RequestInterface $request): ResponseInterface
    {
        $request = $this->authorize($request);

        return $this->getHttpClient()->send($request);
    }

    /**
     * @param RequestInterface $request
     *
     * @return RequestInterface $request
     */
    public function authorize(RequestInterface $request)
    {
        $token = $this->getToken();
        if ($this->isAccessTokenExpired()) {
            if ($this->fetchAccessTokenWithRefreshToken()) {
                $this->saveAccessToken();
            }
        }

        return $request->withHeader('Authorization', "Bearer {$token['access_token']}");
    }

    /**
     * @return array
     */
    public function getToken(): array
    {
        if (!$this->token) {
            $this->token = json_decode($this->tokenSack->getToken(self::TOKEN_NAME), true);
        }

        return $this->token;
    }

    /**
     * @param $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * Returns if the access_token is expired.
     * @return bool Returns True if the access_token is expired.
     */
    public function isAccessTokenExpired(): bool
    {
        if (!$this->token) {
            return true;
        }

        $created = 0;
        if (isset($this->token['created'])) {
            $created = $this->token['created'];
        }

        // If the token is set to expire in the next 30 seconds.
        $expired = ($created + ($this->token['expires'] - 30)) < time();

        return $expired;
    }

    /**
     * @param null $refreshToken
     *
     * @return bool
     */
    public function fetchAccessTokenWithRefreshToken($refreshToken = null): bool
    {
        $refreshToken = $refreshToken ?? $this->token['refresh_token'];
        if (!$refreshToken) {
            throw new LogicException('refresh token must be passed in or set as part of setAccessToken');
        }

        $refreshTokenArray = ['refresh_token' => $refreshToken];
        $accessToken = $this->getAuthorization()->getAccessToken('refresh_token', $refreshTokenArray);
        $credentials = array_merge($accessToken->jsonSerialize(), $refreshTokenArray);


        if (isset($credentials['access_token'])) {
            $this->setToken($credentials);

            return true;
        }

        return false;
    }

    /**
     * @return AbstractProvider
     */
    public function getAuthorization(): AbstractProvider
    {
        if ($this->authorization === null) {
            $this->authorization = new ImgurProvider([
                'clientId' => $this->authKeys['client_id'],
                'clientSecret' => $this->authKeys['client_secret'],
            ]);
        }

        return $this->authorization;
    }

    /**
     * @throws RuntimeException
     */
    public function saveAccessToken()
    {
        if (!$token = json_encode($this->token)) {
            throw new RuntimeException('Invalid token to save');
        }

        $this->tokenSack->saveToken(self::TOKEN_NAME, $token);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient(): \GuzzleHttp\Client
    {
        if (!$this->http) {
            $this->http = new \GuzzleHttp\Client();
        }

        return $this->http;
    }

    /**
     * Attempt to exchange a code for an valid authentication token.
     *
     * @param $code string code from
     *
     * @return bool
     */
    public function fetchAccessTokenWithAuthCode(string $code): bool
    {
        if (strlen($code) == 0) {
            throw new InvalidArgumentException("Invalid code");
        }

        $accessToken = $this->getAuthorization()->getAccessToken('authorization_code', [
            'code' => $code,
        ]);

        $credentials = $accessToken->jsonSerialize();

        if (isset($credentials['access_token'])) {
            $this->setToken($credentials);

            return true;
        }

        return false;
    }

    private function setAuthKeys(array $keyring)
    {
        if (!isset($keyring['client_id'], $keyring['client_secret'])) {
            throw new InvalidParamException('Missing client_id or client_secret in keyring');
        }

        $this->authKeys = $keyring;
    }
}
