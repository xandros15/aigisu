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
     * @param array $context
     *
     * @return bool
     */
    public function validate(array $params, $context = []): bool;

    /**
     * @return array
     */
    public function getErrors(): array;
}
