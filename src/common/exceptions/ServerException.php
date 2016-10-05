<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-28
 * Time: 11:02
 */

namespace Aigisu\Common\Exceptions;


use Exception;
use RuntimeException;

class ServerException extends RuntimeException
{
    const EXCEPTION = 'exception';
    const MESSAGE = 'message';

    public function __construct($message, $code, Exception $previous = null)
    {
        if (!$this->jsonArrayToException($message, $code)) {
            parent::__construct($message, $code, $previous);
        }
    }

    /**
     * @param string $message
     * @param int $code
     * @return bool
     */
    private function jsonArrayToException(string $message, int $code) : bool
    {
        $isJson = false;
        if ($message = json_decode($message, true)) {
            if (isset($message[self::EXCEPTION])) {
                $exception = reset($message[self::EXCEPTION]);
                $this->fillException(array_merge($exception, ['code' => $code]));
            } elseif (isset($message[self::MESSAGE])) {
                $this->fillException(['message' => $message[self::MESSAGE], 'code' => $code]);
            }
            $isJson = true;
        }

        return $isJson;
    }

    private function fillException(array $exception)
    {
        foreach ($exception as $name => $value) {
            if (property_exists($this, $name)) {
                $this->{$name} = $value;
            }
        }
    }
}