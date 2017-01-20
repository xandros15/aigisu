<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-06
 * Time: 13:03
 */

namespace Aigisu\Components\Oauth;


use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository extends AbstractClient implements ClientRepositoryInterface
{

    protected $redirectUrl;

    public function __construct(string $redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    /**
     * Get a client.
     *
     * @param string $clientIdentifier The client's identifier
     * @param string $grantType The grant type used
     * @param null|string $clientSecret The client's secret (if sent)
     * @param bool $mustValidateSecret If true the client must attempt to validate the secret if the client
     *                                        is confidential
     *
     * @return ClientEntityInterface|null
     */
    public function getClientEntity($clientIdentifier, $grantType, $clientSecret = null, $mustValidateSecret = true)
    {
        $user = $this->getUserByNameOrEmail($clientIdentifier);

        return !$user ? null : new ClientEntity([
            'identifier' => $user->getKey(),
            'name' => $user->name,
            'redirectUri' => $this->redirectUrl,
        ]);
    }
}
