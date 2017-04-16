<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 14:29
 */

namespace Aigisu\Components\Validators;


use Aigisu\Components\Validators\Rules\Optional;
use Respect\Validation\Exceptions\NestedValidationException;

class AbstractValidator implements ValidatorInterface
{

    /** @var array */
    protected $errors = [];
    /** @var array */
    protected $context = [];

    /**
     * @param array $params
     * @param array $context
     *
     * @return bool
     */
    public function validate(array $params, $context = []): bool
    {
        $this->context = $context;
        foreach ($this->rules() as $field => $rule) {
            try {
                /** @var $rule \Respect\Validation\Validator */
                $rule->setName(ucfirst($field))->assert($params[$field] ?? null);
            } catch (NestedValidationException $exception) {
                $this->errors[$field] = $exception->getMessages();
            }
        }

        return !$this->errors;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    protected function rules(): array
    {
        return [];
    }

    /**
     * @param array $rules
     *
     * @return array
     */
    protected function makeOptional(array $rules): array
    {
        foreach ($rules as &$rule) {
            $rule = new Optional($rule);
        }

        return $rules;
    }
}
