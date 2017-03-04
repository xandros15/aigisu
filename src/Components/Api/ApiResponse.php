<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 01:57
 */

namespace Aigisu\Components\Api;


use Psr\Http\Message\ResponseInterface;

class ApiResponse
{
    /** @var ResponseInterface */
    private $response;

    /**
     * ApiResponse constructor.
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->response->getStatusCode() >= 400 && $this->response->getStatusCode() < 500;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return json_decode($this->response->getBody(), true)['message'];
    }

    /**
     * @return array
     */
    public function getResponse(): array
    {
        return json_decode($this->response->getBody(), true) ?? [];
    }

    /**
     * @return bool
     */
    public function isForbidden()
    {
        return $this->response->getStatusCode() === 403;
    }
}
