<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-29
 * Time: 20:11
 */

namespace Aigisu\Api\Middlewares\Validators\Exceptions;


use Respect\Validation\Exceptions\ValidationException;

class ImageSizeException extends ValidationException
{
    public static $defaultTemplates = [
        self::MODE_DEFAULT => [
            self::STANDARD => '{{name}} must be a valid image and has resolution between {{minWidth}}x{{minHeight}} ' .
                'and {{maxWidth}}x{{maxHeight}}',
        ],
    ];
}
