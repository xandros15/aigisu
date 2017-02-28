<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-29
 * Time: 20:11
 */

namespace Aigisu\Components\Validators\Exceptions;


use Respect\Validation\Exceptions\ValidationException;

class UnitExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'unit doesn\'t exist',
        ],
    ];
}
