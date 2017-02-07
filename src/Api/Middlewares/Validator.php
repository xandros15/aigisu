<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:31
 */

namespace Aigisu\Api\Middlewares;


use Aigisu\Api\Middlewares\Validators\Rules\Optional;
use Aigisu\Components\Http\BadRequestException;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\UploadedFile;
use SplFileInfo;

abstract class Validator extends Middleware
{
    const MESSAGE = 'message';

    /** @var array */
    protected $errors;

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     * @throws BadRequestException
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if (!$this->validate($request) || !$this->validateFiles($request)) {
            $response = $response->withJson([
                self::MESSAGE => $this->getErrors(),
            ]);

            throw new BadRequestException($request, $response);
        }

        return $next($request, $response);
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function validate(Request $request) : bool
    {
        foreach ($this->rules() as $field => $rule) {
            try {
                /** @var $rule \Respect\Validation\Validator */
                $rule->setName(ucfirst($field))->assert($request->getParam($field));
            } catch (NestedValidationException $exception) {
                $this->errors[$field] = $exception->getMessages();
            }
        }

        return !$this->errors;
    }

    /**
     * @return array
     */
    protected function rules() : array
    {
        return [];
    }

    /**
     * @param Request $request
     * @return bool
     */
    protected function validateFiles(Request $request) : bool
    {
        foreach ($this->fileRules() as $field => $rule) {
            try {
                $rule->setName(ucfirst($field))->assert($this->getUploadedFile($request, $field));
            } catch (NestedValidationException $exception) {
                $this->errors[$field] = $exception->getMessages();
            }
        }

        return !$this->errors;
    }

    /**
     * @return array
     */
    protected function fileRules() : array
    {
        return [];
    }

    /**
     * @param Request $request
     * @param $key
     * @return SplFileInfo|null
     */
    protected function getUploadedFile(Request $request, $key)
    {
        $file = null;

        if (isset($request->getUploadedFiles()[$key])) {
            /** @var $file UploadedFile */
            $file = $request->getUploadedFiles()[$key];
            $file = $file->getError() === UPLOAD_ERR_OK ? new SplFileInfo($file->file) : null;
        }

        return $file;
    }

    /**
     * @return array|null
     */
    protected function getErrors()
    {
        return $this->errors;
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
