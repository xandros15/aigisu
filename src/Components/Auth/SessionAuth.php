<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-23
 * Time: 02:15
 */

namespace Aigisu\Components\Auth;


class SessionAuth
{
    const SESSION_FIELD = 'user_id';

    /**
     * @param int $id
     */
    public function signIn(int $id) : void
    {
        $_SESSION[self::SESSION_FIELD] = $id;
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
        return isset($_SESSION[self::SESSION_FIELD]);
    }

    /**
     * @return int
     */
    public function getAuthId() : int
    {
        return $_SESSION[self::SESSION_FIELD];
    }
}
