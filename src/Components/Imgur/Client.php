<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-03
 * Time: 02:54
 */

namespace Aigisu\Components\Imgur;


use AdamPaterson\OAuth2\Client\Provider\Imgur as ImgurProvider;
use Aigisu\Components\Configure\Configurable;
use Aigisu\Components\TokenSack;
use InvalidArgumentException;
use League\OAuth2\Client\Provider\AbstractProvider;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Client extends Configurable
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

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function execute(RequestInterface $request) : ResponseInterface
    {
        $request = $this->authorize($request);
        return $this->getHttpClient()->send($request);
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface $request
     */
    public function authorize(RequestInterface $request)
    {
        if (!$token = $this->getToken()) {
            // add refresh subscriber to request a new token
            if ($this->isAccessTokenExpired() && isset($this->token['refresh_token'])) {
                if ($this->fetchAccessTokenWithRefreshToken()) {
                    throw new RuntimeException('Can\'t refresh token');
                }
                $this->saveAccessToken();
            }
        }

        return $request->withHeader('Authorization', "Bearer {$token['access_token']}");
    }

    /**
     * @return array
     */
    public function getToken() : array
    {
        if (!$this->token) {
            $this->token = json_decode($this->getTokenSack()->getToken(self::TOKEN_NAME), true);
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
    public function isAccessTokenExpired() : bool
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
     * @return bool
     */
    public function fetchAccessTokenWithRefreshToken($refreshToken = null) : bool
    {
        $refreshToken = $refreshToken ?? $this->token['refresh_token'];
        if (!$refreshToken) {
            throw new LogicException('refresh token must be passed in or set as part of setAccessToken');
        }


        $auth = $this->getAuthorization();
        $refreshTokenArray = ['refresh_token' => $refreshToken];
        $accessToken = $auth->getAccessToken('refresh_token', $refreshTokenArray);
        $credentials = array_merge($accessToken->jsonSerialize(), $refreshTokenArray);


        if ($credentials && isset($credentials['access_token'])) {
            $this->setToken($credentials);
            return true;
        }

        return false;
    }

    /**
     * @return AbstractProvider
     */
    public function getAuthorization() : AbstractProvider
    {
        if ($this->authorization === null) {
            $keys = $this->getAuthKey();
            $this->authorization = new ImgurProvider([
                'clientId' => $keys['client_id'],
                'clientSecret' => $keys['client_secret'],
            ]);
        }

        return $this->authorization;
    }

    /**
     * Parsing auth form config
     *
     * @return array
     */
    public function getAuthKey() : array
    {
        if (!$this->authKeys) {
            if (is_array($this->config['auth'])) {
                $keys = $this->config['auth'];
            } else {
                throw new InvalidArgumentException('Invalid format of keys. Expect array, filename, or json string');
            }

            if (!isset($keys['client_id'], $keys['client_secret'])) {
                throw new LogicException('Missing client keys');
            }

            $this->authKeys = $keys;
        }


        return $this->authKeys;
    }

    /**
     * @throws RuntimeException
     */
    public function saveAccessToken()
    {
        if (!$token = json_encode($this->token)) {
            throw new RuntimeException('Invalid token to save');
        }

        $this->getTokenSack()->saveToken(self::TOKEN_NAME, $token);
    }

    /**
     * @return \GuzzleHttp\Client
     */
    public function getHttpClient() : \GuzzleHttp\Client
    {
        if (is_null($this->http)) {
            $this->http = new \GuzzleHttp\Client();
        }

        return $this->http;
    }

    /**
     * Attempt to exchange a code for an valid authentication token.
     *
     * @param $code string code from
     * @return bool
     */
    public function fetchAccessTokenWithAuthCode(string $code) : bool
    {
        if (strlen($code) == 0) {
            throw new InvalidArgumentException("Invalid code");
        }

        $auth = $this->getAuthorization();

        $accessToken = $auth->getAccessToken('authorization_code', [
            'code' => $code
        ]);

        $credentials = $accessToken->jsonSerialize();

        if ($credentials && isset($credentials['access_token'])) {
            $this->setToken($credentials);
            return true;
        }

        return false;
    }

    /**
     * @return TokenSack
     */
    private function getTokenSack() : TokenSack
    {
        return $this->config[TokenSack::class];
    }
}
