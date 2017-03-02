<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 01:57
 */

namespace Aigisu\Components\Api;


use Slim\Http\Response;

class ApiResponse
{
    /** @var Response */
    private $response;

    /**
     * ApiResponse constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return bool
     */
    public function hasError() : bool
    {
        return $this->response->isClientError();
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return json_decode($this->response->getBody())['message'];
    }

    /**
     * @return array
     */
    public function getResponse() : array
    {
        return json_decode($this->response->getBody())['message'];
    }
}
