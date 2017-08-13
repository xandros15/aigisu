<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-01
 * Time: 16:24
 */

namespace Aigisu\Api\Exceptions;


use Aigisu\Components\Http\Exceptions\BadRequestException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;

class InvalidRecoveryHashException extends BadRequestException
{
    public function __construct(RequestInterface $request, ResponseInterface $response)
    {
        /** @var $response Response */
        $response = $response->withJson([
            'message' => ['token' => 'Invalid recovery hash'],
        ]);
        parent::__construct($request, $response);
    }
}
