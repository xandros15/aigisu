<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-01
 * Time: 20:25
 */

namespace Aigisu\Components\Validators;

use Aigisu\Components\Validators\Rules\EmailExist;
use Respect\Validation\Validator;

class PasswordResetRequestValidator extends AbstractValidator
{
    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'email' => Validator::email()->addRule(new EmailExist($this->context)),
        ];
    }
}
