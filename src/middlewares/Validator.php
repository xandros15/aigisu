<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:31
 */

namespace Middlewares;


use Aigisu\Middleware;
use Api\ApiController;
use Middlewares\Validators\Rules\Optional;
use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class Validator extends Middleware
{
    const OLD_PARAMS = 'old_params';
    const MESSAGE = ApiController::MESSAGE;
    const STATUS_BAD_REQUEST = ApiController::STATUS_BAD_REQUEST;

    /**
     * @var array
     */
    protected $errors;

    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        if ($this->validate($request)) {
            return $next($request, $response);
        }

        return $response->withJson([
            self::MESSAGE => $this->getErrors(),
            self::OLD_PARAMS => $request->getParams()
        ], self::STATUS_BAD_REQUEST);
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function validate(Request $request) : bool
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
    abstract protected function rules() : array;

    /**
     * @return array|null
     */
    public function getErrors()
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