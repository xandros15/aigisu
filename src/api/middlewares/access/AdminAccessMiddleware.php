<?php
/**
 * Created by PhpStorm.
 * User: xandros15
 * Date: 2016-10-09
 * Time: 15:43
 */

namespace Aigisu\Api\Middlewares\Access;


use Aigisu\Api\Models\User;
use Slim\Http\Request;

class AdminAccessMiddleware extends AbstractAccessMiddleware
{

    /**
     * @param Request $request
     * @return bool
     */
    protected function hasAccess(Request $request) : bool
    {
        if ($request->getAttribute('is_guest') === false) {
            /** @var $user User */
            $user = $request->getAttribute('user');
            return $this->compareAccess($user->role, self::class);
        }

        return false;
    }
}
