<?php


namespace Aigisu\Web\Components\Auth;


interface AuthInterface
{
    /**
     * @return bool
     */
    public function isGuest(): bool;

    /**
     * @param array $payload
     *
     * @return bool
     */
    public function signIn(array $payload): bool;


    public function singOut(): void;
}
