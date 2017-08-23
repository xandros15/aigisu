<?php


namespace Aigisu\Web\Components\Auth;


class JWTAuth
{
    private const AUTH_KEY = 'auth';
    private const USER_KEY = 'user_id';
    private const JWT = 'jwt';
    private const EXPIRED = 'exp';
    private const REFRESH_TOKEN = 'ref';

    /**
     * JWTAuth constructor.
     */
    public function __construct()
    {
        if ($this->isExpired() && !$this->getRefreshToken()) {
            $this->singOut();
        }
    }

    /**
     * @return int
     */
    public function getAuthId(): int
    {
        return $_SESSION[self::AUTH_KEY][self::USER_KEY] ?? 0;
    }

    /**
     * @return bool
     */
    public function isGuest(): bool
    {
        return !isset($_SESSION[self::AUTH_KEY][self::USER_KEY]);
    }

    /**
     * @param array $payload
     *
     * @return bool
     */
    public function signIn(array $payload): bool
    {
        $_SESSION[self::AUTH_KEY][self::REFRESH_TOKEN] = $payload['refresh_token'] ?? ($_SESSION[self::AUTH_KEY][self::REFRESH_TOKEN] ?? null);
        $_SESSION[self::AUTH_KEY][self::JWT] = $payload['access_token'];
        $_SESSION[self::AUTH_KEY][self::EXPIRED] = $payload['expires_at'];
        $_SESSION[self::AUTH_KEY][self::USER_KEY] = $payload['user_id'];

        return true;
    }

    /**
     * @return bool
     */
    public function isExpired(): bool
    {
        return isset($_SESSION[self::AUTH_KEY][self::EXPIRED]) && $_SESSION[self::AUTH_KEY][self::EXPIRED] < time();
    }

    public function singOut(): void
    {
        unset($_SESSION[self::AUTH_KEY]);
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $_SESSION[self::AUTH_KEY][self::REFRESH_TOKEN] ?? '';
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $_SESSION[self::AUTH_KEY][self::JWT] ?? '';
    }
}
