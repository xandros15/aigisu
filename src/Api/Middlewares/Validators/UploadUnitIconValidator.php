<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-12
 * Time: 03:02
 */

namespace Aigisu\Api\Middlewares\Validators;


use Aigisu\Api\Middlewares\Validator;
use Aigisu\Api\Middlewares\Validators\Rules\ImageSize;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Http\Request;

class UploadUnitIconValidator extends Validator
{
    /**
     * @param Request $request
     * @return bool
     */
    protected function validateFiles(Request $request) : bool
    {
        $rule = v::size('1KB', '50KB')->addRule(new ImageSize(80, 150));
        $field = 'icon';
        try {
            $rule->setName(ucfirst($field))->assert($this->getUploadedFile($request, 0));//first field in array 'cuz is whole body
        } catch (NestedValidationException $exception) {
            $this->errors[$field] = $exception->getMessages();
        }
        return !$this->errors;
    }
}
