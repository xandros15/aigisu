<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-27
 * Time: 15:14
 */

namespace Aigisu\Api\Middlewares;


use Slim\Http\Request;

abstract class Middleware extends \Aigisu\Core\Middleware
{
    const INDEX = 'id';

    /**
     * @param Request $request
     * @return int
     */
    protected function getID(Request $request) : int
    {
        return $request->getAttribute('route')->getArgument(self::INDEX, 0);
    }
}
