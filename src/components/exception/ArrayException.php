<?php

namespace app\exception;

use Exception;

class ArrayException extends Exception
{

    /**
     * Constructor.
     * @param array $message error messages
     */
    public function __construct($message = null)
    {
        if (is_array($message)) {
            $message = $this->flatten($message);
        }
        parent::__construct($message);
    }

    private function flatten($array)
    {
        $return = '';
        array_walk_recursive(
            $array, function ($a) use (&$return) {
            $return .= $a . PHP_EOL;
        }
        );
        return $return;
    }
}