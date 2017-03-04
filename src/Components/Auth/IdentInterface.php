<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-03-04
 * Time: 13:46
 */

namespace Aigisu\Components\Auth;


interface IdentInterface
{
    /**
     * @return string
     */
    public function email(): string;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return bool
     */
    public function isAdmin(): bool;

    /**
     * @return bool
     */
    public function isOwner(): bool;

}
