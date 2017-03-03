<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 20:39
 */

namespace Aigisu\Components\Http\Exceptions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\SlimException;

class BadRequestException extends SlimException
{
    const BAD_REQUEST_STATUS_CODE = 400;

    /**
     * BadRequestException constructor.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        $response = $response->withStatus(self::BAD_REQUEST_STATUS_CODE);
        parent::__construct($request, $response);
    }
}
