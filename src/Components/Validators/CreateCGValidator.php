<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-09-28
 * Time: 14:20
 */

namespace Aigisu\Components\Validators;


use Aigisu\Components\Validators\Rules\ImageSize;
use Aigisu\Components\Validators\Rules\Optional;
use Aigisu\Models\Unit\CG;
use Respect\Validation\Validator as v;

class CreateCGValidator extends AbstractValidator
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
