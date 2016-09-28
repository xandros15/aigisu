<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-28
 * Time: 14:20
 */

namespace Aigisu\Api\Middlewares\Validators;


use Aigisu\Api\Middlewares\Validator;
use Aigisu\Api\Middlewares\Validators\Rules\ImageSize;
use Aigisu\Api\Middlewares\Validators\Rules\Optional;
use Aigisu\Api\Models\Unit\CG;
use Respect\Validation\Validator as v;

class CreateCGValidator extends Validator
{
    /**
     * @return array
     */
    protected function rules() : array
    {
        return [
            'server' => v::in(CG::getServersNames()),
            'scene' => v::intVal(),
            'archival' => new Optional(v::boolVal()),
        ];
    }

    /**
     * @return array
     */
    protected function fileRules() : array
    {
        return [
            'cg' => v::size('100KB', '1500KB')->addRule(new ImageSize([959, 639], [961, 641])),
        ];
    }
}