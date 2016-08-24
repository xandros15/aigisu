<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-24
 * Time: 10:31
 */

namespace Middlewares;


use Aigisu\Alert\Alert;
use Aigisu\Middleware;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

abstract class Validator extends Middleware
{
    const OLD_PARAMS = 'old';
    const ERRORS = 'errors';
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
            self::ERRORS => $this->getErrors(),
            self::OLD_PARAMS => $request->getParams()
        ], 400);
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
                Alert::add($exception->getFullMessage(), Alert::ERROR);
            }
        }

        return !$this->errors;
    }

    /**
     * @return array
     */
    abstract protected function rules() : array;

    /**
     * @return array
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
            $rule = v::optional($rule);
        }

        return $rules;
    }
}