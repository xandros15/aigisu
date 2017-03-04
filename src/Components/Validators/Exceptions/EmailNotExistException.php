<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 23:16
 */

namespace Aigisu\Components\Validators\Exceptions;


use Respect\Validation\Exceptions\ValidationException;

class EmailNotExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Email is in usage',
        ],
    ];
}
