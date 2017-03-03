<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:33
 */

namespace Aigisu\Components\Validators;


use Aigisu\Components\Validators\Rules\EmailExist;
use Aigisu\Components\Validators\Rules\UserNameExist;
use Respect\Validation\Validator as v;

class CreateUserValidator extends AbstractValidator
{

    /**
     * @return array
     */
    protected function rules() : array
    {
        return [
            'name' => v::stringType()->length(4, 15)->addRule(new UserNameExist($this->context)),
            'email' => v::email()->addRule(new EmailExist($this->context)),
            'password' => v::stringType()->length(8, 32),
        ];
    }
}
