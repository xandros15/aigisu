<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-23
 * Time: 02:15
 */

namespace Aigisu\Components\Auth;


use Aigisu\Models\User;

class SessionAuth
{
    const SESSION_FIELD = 'user_id';

    /**
     * @param string $email
     * @param string $password
     * @return bool
     */
    public function signIn(string $email, string $password) : bool
    {
        if (($user = User::findByEmail($email)) && $user->validatePassword($password)) {
            $_SESSION[self::SESSION_FIELD] = $user->getKey();
            return true;
        }

        return false;
    }

    public function singOut() : void
    {
        unset($_SESSION[self::SESSION_FIELD]);
    }

    /**
     * @return bool
     */
    public function isGuest() : bool
    {
        return !isset($_SESSION[self::SESSION_FIELD]);
    }

    /**
     * @return int
     */
    public function getAuthId() : int
    {
        return $_SESSION[self::SESSION_FIELD];
    }
}
