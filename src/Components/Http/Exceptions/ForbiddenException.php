<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 02:11
 */

namespace Aigisu\Components\Http\Exceptions;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\SlimException;

class ForbiddenException extends SlimException
{
    const FORBIDDEN_STATUS_CODE = 403;

    /**
     * ForbiddenException constructor.
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     */
    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response->withStatus(self::FORBIDDEN_STATUS_CODE));
    }
}
