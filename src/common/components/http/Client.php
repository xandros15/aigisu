<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-28
 * Time: 12:49
 */

namespace Aigisu\Common\Components\Http;


use Aigisu\Common\Exceptions\ServerException;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\RequestOptions;
use Slim\Exception\NotFoundException;

class Client extends \GuzzleHttp\Client
{
    const MESSAGE = 'message';
    const
        STATUS_OK = 200,
        STATUS_CREATED = 201,
        STATUS_BAD_REQUEST = 400,
        STATUS_UNAUTHORIZED = 401,
        STATUS_FORBIDDEN = 403,
        STATUS_NOT_FOUND = 404,
        STATUS_METHOD_NOT_ALLOWED = 405,
        STATUS_SERVER_ERROR = 500;

    /**
     * Client constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $options = [
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
            ],
            RequestOptions::HTTP_ERRORS => false,
        ];

        parent::__construct(array_merge($config, $options));
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return \Psr\Http\Message\ResponseInterface
     * @throws NotFoundException
     * @throws ServerException
     */
    public function request($method, $uri = '', array $options = [])
    {
        $response = parent::request($method, $uri, $options);

        switch ($response->getStatusCode()) {
            case self::STATUS_NOT_FOUND:
                throw new NotFoundException(new ServerRequest($method, $uri), $response);
            case self::STATUS_UNAUTHORIZED:
            case self::STATUS_FORBIDDEN:
            case self::STATUS_METHOD_NOT_ALLOWED:
            case self::STATUS_SERVER_ERROR:
                throw new ServerException($response->getBody(), $response->getStatusCode());
        }

        return $response;
    }
}