<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-27
 * Time: 01:36
 */

namespace Aigisu\Api\Middlewares\Validators\Rules;

use Aigisu\Api\Models\Unit;
use Respect\Validation\Rules\AbstractRule;

class UnitOriginalAvailable extends AbstractRule
{
    /** @var int */
    private $unitID;

    /**
     * UnitOriginalAvailable constructor.
     * @param null|int $unitID
     */
    public function __construct($unitID = null)
    {
        $this->unitID = $unitID;
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        $unit = Unit::where('original', $input);
        if ($this->unitID) {
            $unit->where('id', '!=', $this->unitID);
        }

        return !$unit->exists();
    }
}