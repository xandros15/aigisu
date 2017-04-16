<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-01
 * Time: 18:20
 */

namespace Aigisu\Components\Validators;


use Respect\Validation\Validator as v;

class PasswordResetValidator extends AbstractValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'password' => v::stringType()->length(8, 32),
        ];
    }
}
