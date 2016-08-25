<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-08-08
 * Time: 23:21
 */

namespace Aigisu\Common\Components\View;


interface ViewExtension
{
    public function applyCallbacks(CallbackManager &$callbackManager);
}