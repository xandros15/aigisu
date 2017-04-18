<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2017-02-26
 * Time: 16:19
 */

namespace Aigisu\Components\Validators;


use Aigisu\Middlewares\ValidatorMiddleware;
use Slim\Collection;

class ValidatorManager extends Collection
{
    /**
     * Set collection item
     *
     * @param string $key The data key
     * @param mixed $value The data value
     */
    public function set($key, $value)
    {
        $this->data[$key] = new ValidatorMiddleware($value);
    }
}
