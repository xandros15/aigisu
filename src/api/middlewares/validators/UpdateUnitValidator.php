<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-26
 * Time: 22:55
 */

namespace Aigisu\Api\Middlewares\Validators;


class UpdateUnitValidator extends CreateUnitValidator
{
    /**
     * @return array
     */
    protected function rules() : array
    {
        return $this->makeOptional(parent::rules());
    }

    /**
     * @return array
     */
    protected function fileRules() : array
    {
        return $this->makeOptional(parent::fileRules());
    }
}
