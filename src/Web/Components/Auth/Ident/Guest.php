<?php


namespace Aigisu\Web\Components\Auth\Ident;


class Guest extends User
{
    public function __construct()
    {
        parent::__construct([]);
    }

    public function name(): string
    {
        return 'guest';
    }
}
