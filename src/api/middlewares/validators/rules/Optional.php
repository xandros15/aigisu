<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 14:33
 */

namespace Aigisu\Api\Middlewares\Validators\Rules;


use Respect\Validation\Rules\AbstractWrapper;
use Respect\Validation\Validatable;

/**
 * Class Optional
 * @package Middlewares\Validators\Rules
 * @see https://github.com/Respect/Validation/blob/master/library/Rules/Optional.php
 */
class Optional extends AbstractWrapper
{

    public function __construct(Validatable $rule)
    {
        $this->validatable = $rule;
    }

    public function assert($input)
    {
        if ($this->isOptional($input)) {
            return true;
        }

        return parent::assert($input);
    }

    private function isOptional($input)
    {
        return $input === null;
    }

    public function check($input)
    {
        if ($this->isOptional($input)) {
            return true;
        }

        return parent::check($input);
    }

    public function validate($input)
    {
        if ($this->isOptional($input)) {
            return true;
        }

        return parent::validate($input);
    }
}