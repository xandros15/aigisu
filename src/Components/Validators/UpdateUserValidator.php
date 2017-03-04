<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-01
 * Time: 20:51
 */

namespace Aigisu\Components\Validators;


use Aigisu\Components\Validators\Rules\EmailNotExist;
use Respect\Validation\Validator as v;

class UpdateUserValidator extends AbstractValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return $this->makeOptional([
            'name' => v::stringType()->length(4, 15),
            'email' => v::email()->addRule(new EmailNotExist($this->context)),
        ]);
    }
}
