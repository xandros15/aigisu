<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 13:49
 */

namespace Aigisu\Components\Oauth;


use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

class UserEntity implements UserEntityInterface
{
    use EntityTrait;

    /**
     * UserEntity constructor.
     * @param $identifier
     */
    public function __construct($identifier)
    {
        $this->setIdentifier($identifier);
    }
}