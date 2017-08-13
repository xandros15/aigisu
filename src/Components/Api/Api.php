<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 01:53
 */

namespace Aigisu\Components\Api;


use Aigisu\Components\Http\Exceptions\ForbiddenException;
use Aigisu\Components\Http\Exceptions\HttpException;
use Aigisu\Components\Http\Exceptions\RuntimeException;
use Aigisu\Web\Components\JwtAuth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\RequestOptions;
use Lcobucci\JWT\Signer\Key;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Slim\Exception\NotFoundException;

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
        $response = $this->client->post('auth', [
            RequestOptions::FORM_PARAMS => [
                'email' => $email,
                'password' => $password,
            ],
        ]);


        $response = new ApiResponse($response);

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
        } catch (HttpException | ClientException | ServerException $e) {
            //@todo exception handling
            throw new RuntimeException($request, $e->getResponse());
        } catch (NotFoundException $e) {
            $request = $e->getRequest()->withHeader('Accept', 'text/html');
            throw new NotFoundException($request, $e->getResponse());
        }
        $apiResponse = new ApiResponse($response);

        if ($apiResponse->isForbidden()) {
            throw new ForbiddenException(ServerRequest::fromGlobals(), $response);
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
    public function request(string $path, string $method = 'GET', StreamInterface $body = null): ApiResponse
    {
        $request = new Request($method, ltrim($path, '/'), [], $method == 'GET' ? null : $body);

        return $this->send($request);
    }
}
