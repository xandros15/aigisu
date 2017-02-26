<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-12
 * Time: 03:02
 */

namespace Aigisu\Components\Validators;


use Aigisu\Components\Validators\Rules\ImageSize;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class UploadUnitIconValidator extends AbstractValidator
{
    const FIELD = 'icon';

    /**
     * @param array $files
     * @return bool
     */
    public function validateFiles(array $files) : bool
    {
        $rule = v::size('1KB', '50KB')->addRule(new ImageSize(80, 150));
        try {
            //first field in array 'cuz is whole body
            $rule->setName(ucfirst(self::FIELD))->assert($this->getUploadedFile($files, 0));
        } catch (NestedValidationException $exception) {
            $this->errors[self::FIELD] = $exception->getMessages();
        }

        return !$this->errors;
    }
}
