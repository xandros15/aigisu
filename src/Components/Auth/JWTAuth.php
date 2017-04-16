<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-09
 * Time: 00:08
 */

namespace Aigisu\Components\Auth;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;

class JWTAuth
{
    const EXPIRATION = 3600; //1 hour
    const DELAY = 0; //0 min

    /** @var Builder */
    private $private;
    private $public;

    /**
     * JWTAuth constructor.
     *
     * @param array $keyring
     */
    public function __construct(array $keyring)
    {
        foreach ($keyring as $name => $key) {
            if (!$key instanceof Key) {
                throw new InvalidParamException("Wrong key: {$name}");
            }
        }

        $this->private = $keyring['private'];
        $this->public = $keyring['public'];
    }

    /**
     * @param string $id
     *
     * @return Token
     */
    public function createToken(string $id)
    {
        return (new Builder())
            ->setIssuedAt(time())// Configures the time that the token was issue (iat claim)
            ->setNotBefore(time() + self::DELAY)// Configures the time that the token can be used (nbf claim)
            ->setExpiration(time() + self::EXPIRATION)// Configures the expiration time of the token (nbf claim)
            ->setId($id, true)
            ->sign(new Sha256(), $this->private)
            ->getToken();
    }

    /**
     * @param Token $token
     *
     * @return bool
     */
    public function verifyToken(Token $token): bool
    {
        return $token->verify(new Sha256(), $this->public) && $token->validate(new ValidationData());
    }
}
