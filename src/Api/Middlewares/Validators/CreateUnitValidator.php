<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-26
 * Time: 22:55
 */

namespace Aigisu\Api\Middlewares\Validators;


use Aigisu\Api\Middlewares\Validator;
use Aigisu\Api\Middlewares\Validators\Rules\ImageSize;
use Aigisu\Api\Middlewares\Validators\Rules\Optional;
use Aigisu\Api\Models\Unit;
use Respect\Validation\Validator as v;

class CreateUnitValidator extends Validator
{

    /**
     * @return array
     */
    protected function rules() : array
    {
        return [
            'name' => v::alpha('_')->noWhitespace(),
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

    /**
     * @return array
     */
    protected function fileRules() : array
    {
        return [
            'icon' => v::size('1KB', '50KB')->addRule(new ImageSize(80, 150)),
        ];
    }
}
