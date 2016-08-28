<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-28
 * Time: 11:24
 */

namespace Aigisu\Common\Exceptions;


interface JsonException
{
    /**
     * @param string $json
     * @return JsonException
     */
    public function jsonToException(string $json) : static;
}