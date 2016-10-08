<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-08
 * Time: 17:18
 */

namespace Aigisu\Components\Oauth;


use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ResponseInterface;

class BearerTokenResponse extends \League\OAuth2\Server\ResponseTypes\BearerTokenResponse implements ResponseTypeInterface
{
    /**
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function generateHttpResponse(ResponseInterface $response)
    {
        $expireDateTime = $this->accessToken->getExpiryDateTime()->getTimestamp();

        $jwtAccessToken = $this->accessToken->convertToJWT($this->privateKey);

        $responseParams = [
            'token_type' => 'Bearer',
            'expires' => $expireDateTime,
            'access_token' => (string) $jwtAccessToken,
        ];

        if ($this->refreshToken instanceof RefreshTokenEntityInterface) {
            $refreshToken = $this->encrypt(json_encode([
                'client_id' => $this->accessToken->getClient()->getIdentifier(),
                'refresh_token_id' => $this->refreshToken->getIdentifier(),
                'access_token_id' => $this->accessToken->getIdentifier(),
                'scopes' => $this->accessToken->getScopes(),
                'expire_time' => $this->refreshToken->getExpiryDateTime()->getTimestamp(),
            ]));

            $responseParams['refresh_token'] = $refreshToken;
        }

        $responseParams = array_merge($this->getExtraParams($this->accessToken), $responseParams);

        $response = $response
            ->withStatus(200)
            ->withHeader('pragma', 'no-cache')
            ->withHeader('cache-control', 'no-store')
            ->withHeader('content-type', 'application/json; charset=UTF-8');

        $response->getBody()->write(json_encode($responseParams));

        return $response;
    }
}