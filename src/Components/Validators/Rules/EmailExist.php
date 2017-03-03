<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-02
 * Time: 23:16
 */

namespace Aigisu\Components\Validators\Rules;


use Aigisu\Models\User;
use Respect\Validation\Rules\AbstractRule;

class EmailExist extends AbstractRule
{
    /** @var null */
    private $id;

    /**
     * EmailExist constructor.
     * @param $context
     */
    public function __construct($context)
    {
        $this->id = $context['id'] ?? null;
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        $user = User::where('email', $input);
        if ($this->id) {
            $user = $user->where('id', '!=', $this->id);
        }
        return $user->exists();
    }
}
