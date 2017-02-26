<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 14:29
 */

namespace Aigisu\Components\Validators;


use Aigisu\Components\Http\UploadedFile;
use Aigisu\Components\Validators\Rules\Optional;
use Respect\Validation\Exceptions\NestedValidationException;
use SplFileInfo;

class AbstractValidator implements ValidatorInterface
{

    /** @var array */
    protected $errors = [];

    /**
     * @param array $params
     * @return bool
     */
    public function validate(array $params) : bool
    {
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
     * @param array $files
     * @return bool
     */
    public function validateFiles(array $files) : bool
    {
        foreach ($this->fileRules() as $field => $rule) {
            try {
                $rule->setName(ucfirst($field))->assert($this->getUploadedFile($files, $field));
            } catch (NestedValidationException $exception) {
                $this->errors[$field] = $exception->getMessages();
            }
        }

        return !$this->errors;
    }

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * @return array
     */
    protected function rules() : array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function fileRules() : array
    {
        return [];
    }

    /**
     * @param array $files
     * @param $key
     * @return UploadedFile|null
     */
    protected function getUploadedFile(array $files, $key)
    {
        $file = null;

        if (isset($files[$key])) {
            /** @var $file UploadedFile */
            $file = $files[$key];
            $file = $file->getError() === UPLOAD_ERR_OK ? new SplFileInfo($file->file) : null;
        }

        return $file;
    }

    /**
     * @param array $rules
     * @return array
     */
    protected function makeOptional(array $rules) : array
    {
        foreach ($rules as &$rule) {
            $rule = new Optional($rule);
        }

        return $rules;
    }
}
