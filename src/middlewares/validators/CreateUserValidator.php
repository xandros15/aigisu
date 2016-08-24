<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:33
 */

namespace Middlewares\Validators;


use Middlewares\Validator;
use Respect\Validation\Validator as v;

class CreateUserValidator extends Validator
{
    /**
     * @return array
     */
    protected function rules() : array
    {
        return [
            'name' => v::stringType()->length(4, 15),
            'email' => v::email(),
            'password' => v::stringType()->length(8, 32)
        ];
    }
}