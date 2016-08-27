<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-26
 * Time: 22:55
 */

namespace Aigisu\Api\Middlewares\Validators;


use Aigisu\Api\Middlewares\Validator;
use Aigisu\Api\Middlewares\Validators\Rules\Optional;
use Aigisu\Api\Middlewares\Validators\Rules\UnitOriginalAvailable;
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
            'original' => v::stringType()->addRule(new UnitOriginalAvailable()),
            'icon' => v::url(),
            'link' => new Optional(v::url()),
            'linkgc' => v::url(),
            'rarity' => v::in(Unit::getRarities()),
            'is_male' => v::boolVal(),
            'is_only_dmm' => v::boolVal(),
            'has_aw_image' => v::boolVal(),
            'tags' => v::arrayType()
        ];
    }
}