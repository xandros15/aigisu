<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 18:45
 */

namespace Api;


use Aigisu\Controller;

class ApiController extends Controller
{
    const MESSAGE = 'message';
    const
        STATUS_OK = 200,
        STATUS_CREATED = 201,
        STATUS_BAD_REQUEST = 400,
        STATUS_UNAUTHORIZED = 401,
        STATUS_FORBIDDEN = 403,
        STATUS_NOT_FOUND = 404,
        STATUS_METHOD_NOT_ALLOWED = 405,
        STATUS_SERVER_ERROR = 500;
}