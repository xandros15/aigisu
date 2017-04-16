<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-03
 * Time: 14:29
 */

namespace Aigisu\Components\Validators\Rules;


use Aigisu\Models\User;
use Respect\Validation\Rules\AbstractRule;

class UserNameExist extends AbstractRule
{
    /** @var null|int */
    private $id;

    /**
     * UserNameExist constructor.
     *
     * @param $context
     */
    public function __construct($context)
    {
        $this->id = $context['id'] ?? null;
    }


    /**
     * @param $input
     *
     * @return bool
     */
    public function validate($input)
    {
        $user = User::where('name', $input);
        if ($this->id) {
            $user = $user->where('id', '!=', $this->id);
        }

        return !$user->exists();
    }
}
