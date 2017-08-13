<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 02:24
 */

namespace Aigisu\Components\Http\Exceptions;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UnauthorizedException extends HttpException
{
    const UNAUTHORIZED_STATUS_CODE = 401;

    /**
     * ForbiddenException constructor.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response->withStatus(self::UNAUTHORIZED_STATUS_CODE));
    }
}
