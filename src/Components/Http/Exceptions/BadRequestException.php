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

class BadRequestException extends HttpException
{
    const BAD_REQUEST_STATUS_CODE = 400;

    /**
     * BadRequestException constructor.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response->withStatus(self::BAD_REQUEST_STATUS_CODE));
    }
}
