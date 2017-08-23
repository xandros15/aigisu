<?php


namespace Aigisu\Web\Components\Auth\Ident;


use Slim\Collection;

class User extends Collection
{
    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->get('role') == 'admin';
    }

    /**
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->get('role') == 'owner';
    }

    /**
     * @return string
     */
    public function email(): string
    {
        return $this->get('email');
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->get('name');
    }
}
