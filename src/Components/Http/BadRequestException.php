<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 20:39
 */

namespace Aigisu\Components\Http;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\SlimException;

class BadRequestException extends SlimException
{
    const STATUS_BAD_REQUEST = 400;

    /**
     * BadRequestException constructor.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        $response = $response->withStatus(self::STATUS_BAD_REQUEST);
        parent::__construct($request, $response);
    }
}