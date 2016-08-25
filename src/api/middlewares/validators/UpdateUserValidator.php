<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 11:11
 */

namespace Aigisu\Api\Middlewares\Validators;


class UpdateUserValidator extends CreateUserValidator
{
    /**
     * @return array
     */
    protected function rules() : array
    {
        return $this->makeOptional(parent::rules());
    }
}