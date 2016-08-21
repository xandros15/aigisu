<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-21
 * Time: 17:30
 */

namespace Aigisu;


use Aigisu\Alert\Alert;
use Respect\Validation\Exceptions\NestedValidationException;

trait Validator
{
    /**
     * @var array
     */
    protected $errors;

    /**
     * @param array $toValidate
     * @return bool
     */
    public function validate(array $toValidate = [])
    {
        $rules = $toValidate ? array_intersect_key($this->rules(), array_flip($toValidate)) : $this->rules();

        foreach ($rules as $field => $rule) {
            try {
                /** @var $rule \Respect\Validation\Validator */
                $rule->setName(ucfirst($field))->assert($this->{$field} ?? null);
            } catch (NestedValidationException $exception) {
                $this->errors[$field] = $exception->getMessages();
                Alert::add($exception->getFullMessage(), Alert::ERROR);
            }
        }

        return !$this->errors;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    abstract protected function rules() : array;
}