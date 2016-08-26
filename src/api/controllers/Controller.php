<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 18:45
 */

namespace Aigisu\Api\Controllers;


use Aigisu\Api\Messages;
use Aigisu\Core\ActiveContainer;
use Slim\Http\Request;

class Controller extends ActiveContainer implements Messages
{
    const EXTENDED = 'extended';
    const INDEX = 'id';

    protected function getExtendedParam(Request $request)
    {
        return $request->getQueryParam(self::EXTENDED, []);
    }

    protected function getID(Request $request) : int
    {
        return $request->getAttribute(self::INDEX, -1);
    }
}