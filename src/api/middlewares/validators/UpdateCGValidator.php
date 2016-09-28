<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-28
 * Time: 14:49
 */

namespace Aigisu\Api\Middlewares\Validators;


class UpdateCGValidator extends CreateCGValidator
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