<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-31
 * Time: 14:34
 */

namespace Aigisu\Api\Models\Events;


use Aigisu\Core\Model;

interface Event
{
    /**
     * @param Model $model
     * @return void
     */
    public function __invoke(Model $model);
}