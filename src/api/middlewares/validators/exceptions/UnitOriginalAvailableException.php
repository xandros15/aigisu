<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-27
 * Time: 01:40
 */

namespace Aigisu\Api\Middlewares\Validators\Exceptions;

use Respect\Validation\Exceptions\ValidationException;

class UnitOriginalAvailableException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Original name has been taken.'
        ]
    ];
}