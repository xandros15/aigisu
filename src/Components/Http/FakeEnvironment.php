<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-04
 * Time: 16:09
 */

namespace Aigisu\Components\Http;


use Slim\Http\Environment;

class FakeEnvironment extends Environment
{
    public function __construct()
    {
        parent::__construct([]);
    }
}
