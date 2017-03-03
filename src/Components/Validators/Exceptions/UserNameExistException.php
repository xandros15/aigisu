<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 14:30
 */

namespace Aigisu\Components\Validators\Exceptions;


use Respect\Validation\Exceptions\ValidationException;

class UserNameExistException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => 'Name is in usage',
        ],
    ];
}
