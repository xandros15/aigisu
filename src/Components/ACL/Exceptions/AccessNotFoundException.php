<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-27
 * Time: 21:22
 */

namespace Aigisu\Components\ACL\Exceptions;


use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

class AccessNotFoundException extends Exception implements NotFoundExceptionInterface, ContainerExceptionInterface
{

}
