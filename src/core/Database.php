<?php

namespace Aigisu\Core;

use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;

class Database extends Capsule
{

    public function __construct(array $connection)
    {
        $container = new Container();
        Container::setInstance($container);
        parent::__construct($container);
        $this->addConnection($connection);
    }
}