<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 01:53
 */

namespace Aigisu\Components\Api;


use Aigisu\Components\Http\Exceptions\ForbiddenException;
use Aigisu\Web\Components\JwtAuth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Signer\Key;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Exception\NotFoundException;
use function GuzzleHttp\Psr7\stream_for;

class Api
{
    /** @var Client */
    private $client;
    /** @var JwtAuth */
    private $auth;

    /**
     * Api constructor.
     *
     * @param string $baseUri
     */
    public function __construct(string $baseUri)
    {
        $this->auth = new JwtAuth();
        $this->client = new Client([
            'base_uri' => $baseUri,
            RequestOptions::COOKIES => true,
            RequestOptions::CONNECT_TIMEOUT => 3,
        ]);
    }

    public function auth(string $email, string $password, Key $public)
    {
        $response = $this->request('/auth', 'POST', [
            'email' => $email,
            'password' => $password,
        ]);

        if (!$response->hasError()) {
            $this->auth->signIn($response->getArrayBody()['token'], $public);

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param RequestInterface $request
     *
     * @return ApiResponse
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function send(RequestInterface $request): ApiResponse
    {
        $options = [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
        ];

        if (!$this->auth->isGuest()) {
            $options[RequestOptions::HEADERS]['Authorization'] = 'Bearer ' . $this->auth->getToken();
        }

        try {
            $response = $this->client->send($request, $options);
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            if ($response->getStatusCode() == 404) {
                throw $exception;
            }
        }

        $apiResponse = new ApiResponse($response);

        if ($apiResponse->isForbidden()) {
            throw new ForbiddenException($response, $response);
        }


        return $apiResponse;
    }

    /**
     * @param string $path
     * @param string $method
     * @param StreamInterface|null $body
     *
     * @return ApiResponse
     */
    public function request(string $path, string $method = 'GET', $body = null): ApiResponse
    {
        $headers = [];
        if (is_array($body)) {
            $body = stream_for(http_build_query($body));
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if ($body !== null && !$body instanceof StreamInterface) {
            throw new \InvalidArgumentException('Param body must be a instance of PSR-7 StreamInterface or array');
        }

        $request = new Request($method, ltrim($path, '/'), $headers, $method == 'GET' ? null : $body);

        return $this->send($request);
    }
}
