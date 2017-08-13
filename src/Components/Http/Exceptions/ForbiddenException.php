<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 02:11
 */

namespace Aigisu\Components\Http\Exceptions;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ForbiddenException extends HttpException
{
    const FORBIDDEN_STATUS_CODE = 403;

    /**
     * ForbiddenException constructor.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response->withStatus(self::FORBIDDEN_STATUS_CODE));
    }
}
