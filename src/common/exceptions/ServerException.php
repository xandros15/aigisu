<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-28
 * Time: 11:02
 */

namespace Aigisu\Common\Exceptions;


use Slim\Exception\SlimException;

class ServerException extends SlimException implements JsonException
{
    const EXCEPTION = 'exception';

    /**
     * @param string $json
     * @return ServerException
     */
    public function jsonToException(string $json)
    {
        $instance = json_decode($json, true);
        $exception = reset($instance[self::EXCEPTION]);

        if (is_array($exception)) {
            foreach ($exception as $name => $value) {
                $this->{$name} = $value;
            }
        }

        return $this;
    }
}