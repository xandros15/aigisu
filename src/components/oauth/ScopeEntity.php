<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 13:49
 */

namespace Aigisu\Components\Oauth;


use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ScopeEntity implements ScopeEntityInterface
{
    use EntityTrait;

    /**
     * Create a new scope instance.
     *
     * @param  string $name
     */
    public function __construct(string $name)
    {
        $this->setIdentifier($name);
    }

    public function jsonSerialize()
    {
        return $this->getIdentifier();
    }
}