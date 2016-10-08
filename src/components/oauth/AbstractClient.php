<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-08
 * Time: 20:17
 */

namespace Aigisu\Components\Oauth;


use Aigisu\Api\Models\User;

abstract class AbstractClient
{
    /**
     * @param $identify
     * @return User|null
     */
    protected function getUserByNameOrEmail($identify)
    {
        if (filter_var($identify, FILTER_VALIDATE_EMAIL) !== false) {
            $user = User::where('email', $identify)->first();
        } else {
            $user = User::where('name', $identify)->first();
        }

        return $user;
    }
}