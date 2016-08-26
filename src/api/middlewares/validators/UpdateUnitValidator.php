<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-26
 * Time: 22:55
 */

namespace Aigisu\Api\Middlewares\Validators;


use Aigisu\Api\Middlewares\Validator;

class UpdateUnitValidator extends Validator
{

    /**
     * @return array
     */
    protected function rules() : array
    {
        return $this->makeOptional(parent::rules());
    }
}