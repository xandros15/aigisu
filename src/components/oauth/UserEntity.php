<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 13:49
 */

namespace Aigisu\Components\Oauth;


use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{

    /**
     * Return the user's identifier.
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return 1;
    }
}