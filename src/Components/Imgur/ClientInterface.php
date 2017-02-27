<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-27
 * Time: 16:33
 */

namespace Aigisu\Components\Imgur;


use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ClientInterface
{
    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     */
    public function execute(RequestInterface $request) : ResponseInterface;

}
