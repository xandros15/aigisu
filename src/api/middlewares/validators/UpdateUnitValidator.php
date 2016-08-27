<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-26
 * Time: 22:55
 */

namespace Aigisu\Api\Middlewares\Validators;


use Aigisu\Api\Middlewares\Validators\Rules\UnitOriginalAvailable;
use Respect\Validation\Validator as v;
use Slim\Http\Request;
use Slim\Http\Response;

class UpdateUnitValidator extends CreateUnitValidator
{
    /** @var int */
    private $unitID;

    /**
     * @param Request $request
     * @param Response $response
     * @param callable $next
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next) : Response
    {
        $this->unitID = $this->getID($request);
        return parent::__invoke($request, $response, $next);
    }

    /**
     * @return array
     */
    protected function rules() : array
    {
        $rules = [
            'original' => v::stringType()->addRule(new UnitOriginalAvailable($this->unitID)),
        ];
        return $this->makeOptional(array_merge(parent::rules(), $rules));
    }
}