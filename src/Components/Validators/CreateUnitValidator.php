<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-26
 * Time: 22:55
 */

namespace Aigisu\Components\Validators;


use Aigisu\Components\Validators\Rules\Optional;
use Aigisu\Models\Unit;
use Respect\Validation\Validator as v;

class CreateUnitValidator extends AbstractValidator
{

    /**
     * @return array
     */
    protected function rules(): array
    {
        return [
            'name' => v::alpha('_'),
            'japanese_name' => v::stringType(),
            'link_seesaw' => new Optional(v::url()),
            'link_gc' => v::url(),
            'rarity' => v::in(Unit::getRarities()),
            'gender' => v::in(Unit::getGenders()),
            'dmm' => v::boolVal(),
            'nutaku' => v::boolVal(),
            'special_cg' => v::boolVal(),
            'tags' => new Optional(v::arrayType()),
        ];
    }
}
