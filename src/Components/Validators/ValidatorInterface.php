<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 15:13
 */

namespace Aigisu\Components\Validators;


interface ValidatorInterface
{
    /**
     * @param array $params
     * @return bool
     */
    public function validate(array $params) : bool;

    /**
     * @param array $files
     * @return bool
     */
    public function validateFiles(array $files) : bool;

    /**
     * @return array
     */
    public function getErrors() : array;
}
