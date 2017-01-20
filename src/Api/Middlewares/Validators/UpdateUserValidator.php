<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 11:11
 */

namespace Aigisu\Api\Middlewares\Validators;


use Respect\Validation\Validator as v;

class UpdateUserValidator extends CreateUserValidator
{
    /**
     * @return array
     */
    protected function rules() : array
    {
        return $this->makeOptional([
            'name' => v::stringType()->length(4, 15),
            'email' => v::email(),
            'role' => v::in($this->getEnumRoles()),
        ]);
    }
}
