<?php


namespace Aigisu\Web\Components;


use Firebase\JWT\JWT;
use Lcobucci\JWT\Signer\Key;

class JwtAuth
{
    private const USER_KEY = 'user_id';
    private const JWT = 'jwt';
    private const EXPIRED = 'exp';

    /**
     * @return int
     */
    public function getAuthId(): int
    {
        if ($this->isExpired() || !isset($_SESSION[self::USER_KEY])) {
            $this->singOut();
        }

        return $_SESSION[self::USER_KEY] ?? 0;
    }

    /**
     * @return bool
     */
    public function isGuest(): bool
    {
        if ($this->isExpired() || !isset($_SESSION[self::USER_KEY])) {
            $this->singOut();
        }

        return !isset($_SESSION[self::USER_KEY]);
    }

    /**
     * @param $token
     * @param Key $public
     *
     * @return bool
     */
    public function signIn($token, Key $public): bool
    {
        try {
            $jwt = JWT::decode($token, $public->getContent(), ['RS256']);
            $_SESSION[self::JWT] = $token;
            $_SESSION[self::EXPIRED] = $jwt->exp;
            $_SESSION[self::USER_KEY] = $jwt->jti;
        } catch (\UnexpectedValueException $exception) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return isset($_SESSION[self::EXPIRED]) && $_SESSION[self::EXPIRED] < time();
    }

    public function singOut(): void
    {
        unset($_SESSION[self::USER_KEY], $_SESSION[self::EXPIRED], $_SESSION[self::JWT]);
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        if ($this->isExpired() || !isset($_SESSION[self::JWT])) {
            $this->singOut();
        }

        return $_SESSION[self::JWT] ?? '';
    }
}
