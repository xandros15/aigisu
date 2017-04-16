<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-28
 * Time: 02:09
 */

namespace Aigisu\Components\Validators\Rules;


use Aigisu\Models\Unit;
use Respect\Validation\Rules\AbstractRule;

class UnitExist extends AbstractRule
{

    /**
     * @param int $id
     *
     * @return bool
     */
    public function validate($id)
    {
        return (bool) Unit::find((int) $id);
    }
}
