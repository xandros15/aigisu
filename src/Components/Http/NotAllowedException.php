<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-07
 * Time: 02:11
 */

namespace Aigisu\Components\Http;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\SlimException;

class NotAllowedException extends SlimException
{
    public function __construct(ServerRequestInterface $request, ResponseInterface $response)
    {
        parent::__construct($request, $response->withStatus(403));
    }

}