<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 17:58
 */

namespace Aigisu\Components\Oauth;


use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

class ClientEntity implements ClientEntityInterface
{

    use ClientTrait, EntityTrait;

    /**
     * ClientEntity constructor.
     * @param array $params
     */
    public function __construct(array $params)
    {
        foreach ($params as $param => $value) {
            if (property_exists($this, $param)) {
                $this->{$param} = $value;
            }
        };
    }

}
