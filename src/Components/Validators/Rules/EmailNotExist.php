<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-04
 * Time: 17:41
 */

namespace Aigisu\Components\Validators\Rules;


class EmailNotExist extends EmailExist
{
    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return !parent::validate($input);
    }
}
