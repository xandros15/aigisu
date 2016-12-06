<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-03
 * Time: 02:54
 */

namespace Aigisu\Components\Imgur;


use AdamPaterson\OAuth2\Client\Provider\Imgur as ImgurProvider;
use InvalidArgumentException;
use League\OAuth2\Client\Provider\AbstractProvider;
use LogicException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class Client
{
    const ENDPOINT = 'https://api.imgur.com/3/';

    /** @var \GuzzleHttp\Client */
    private $http;
    /** @var Imgur */
    private $authorization;
    /** @var Config */
    private $config;
    /** @var array */
    private $token;
    /** @var array */
    private $authKeys;

    public function __construct(array $config = [])
    {
        $this->config = new Config($config);
    }

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
        if (!$token = $this->getAccessToken()) {
            $token = $this->setAccessToken($this->config['access-file']);
            // add refresh subscriber to request a new token
            if ($this->isAccessTokenExpired() && isset($this->token['refresh_token'])) {
                $token = $this->fetchAccessTokenWithRefreshToken();
                $this->saveAccessToken();
            }
        }

        return $request->withHeader('Authorization', "Bearer {$token['access_token']}");
    }

    /**
     * @return array
     */
    public function getAccessToken() : array
    {
        return $this->token ?: [];
    }

    /**
     * @param string|array $token
     * @return array
     * @throws InvalidArgumentException
     */
    public function setAccessToken($token) : array
    {
        if (is_string($token)) {
            if (file_exists($token)) {
                $token = file_get_contents($token);
            }
            if ($json = json_decode($token, true)) {
                $token = $json;
            } else {
                // assume $token is just the token string
                $token = ['access_token' => $token];
            }
        }

        if (empty($token)) {
            throw new InvalidArgumentException('invalid json token');
        }

        if (!isset($token['access_token'])) {
            throw new InvalidArgumentException("Invalid token format");
        }

        return $this->token = $token;
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
     * @throws LogicException
     * @return array
     */
    public function fetchAccessTokenWithRefreshToken($refreshToken = null) : array
    {
        if (is_null($refreshToken)) {
            if (!isset($this->token['refresh_token'])) {
                throw new LogicException('refresh token must be passed in or set as part of setAccessToken');
            }
            $refreshToken = $this->token['refresh_token'];
        }

        $auth = $this->getAuthorization();
        $refreshTokenArray = ['refresh_token' => $refreshToken];
        $accessToken = $auth->getAccessToken('refresh_token', $refreshTokenArray);
        $credentials = array_merge($accessToken->jsonSerialize(), $refreshTokenArray);


        if ($credentials && isset($credentials['access_token'])) {
            $this->setAccessToken($credentials);
        }

        return $credentials;
    }

    /**
     * @return AbstractProvider
     */
    public function getAuthorization() : AbstractProvider
    {
        if (is_null($this->authorization)) {
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
            $auth = $this->config['auth'];
            if (is_string($auth)) {
                if (file_exists($auth)) {
                    $auth = file_get_contents($auth);
                }

                if (!$keys = json_decode($auth, true)) {
                    throw new InvalidArgumentException('Invalid format of auth keys file');
                }
            } elseif (is_array($auth)) {
                $keys = $auth;
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
     * @param string $filename
     * @param array $token
     * @return int
     * @throws RuntimeException
     */
    public function saveAccessToken(string $filename = '', array $token = [])
    {
        if (!$filename) {
            $filename = $this->config['access-file'];
        }

        if (!$filename) {
            throw new RuntimeException('No filename is set');
        }

        if (!realpath($dirname = dirname($filename))) {
            throw new RuntimeException("Can't create/update credentials file. Path {$dirname} no exist");
        }

        if (!$token = json_encode($token ?: $this->token)) {
            throw new RuntimeException('Invalid token to save');
        }

        return file_put_contents($filename, $token);
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
     * @return array access token
     */
    public function fetchAccessTokenWithAuthCode(string $code)
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
            $this->setAccessToken($credentials);
        }

        return $credentials;
    }
}