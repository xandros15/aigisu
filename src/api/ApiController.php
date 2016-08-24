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
        STATUS_NOT_FOUND = 404,
        STATUS_BAD_REQUEST = 400,
        STATUS_SERVER_ERROR = 500;

}