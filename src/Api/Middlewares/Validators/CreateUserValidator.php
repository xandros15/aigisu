<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:33
 */

namespace Aigisu\Api\Middlewares\Validators;


use Aigisu\Api\Middlewares\Validator;
use InvalidArgumentException;
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
            'password' => v::stringType()->length(8, 32),
            'role' => v::in($this->getEnumRoles()),
        ];
    }

    /**
     * @return array
     */
    protected function getEnumRoles() : array
    {
        $accesses = $this->get('access');
        $roles = [];
        foreach ($accesses as $access) {
            $roles[] = $access['role'];
        }

        if (!$roles) {
            throw new InvalidArgumentException('Missing roles in access param. Check configuration params');
        }

        return $roles;
    }
}
