<?php


namespace Aigisu\Web\Components\Auth;


use Aigisu\Components\Api\Api;
use Aigisu\Web\Components\Auth\Ident\User;
use Pimple\Container;

class Ident extends Container
{
    /** @var Api */
    private $api;
    /** @var JWTAuth */
    private $auth;

    public function __construct(Api $api, JWTAuth $auth)
    {
        $this->api = $api;
        $this->auth = $auth;
        $this->prepare();
        parent::__construct();
    }

    private function prepare()
    {
        $this['user'] = function (Ident $ident) {
            if (!$ident['is_guest']) {
                return new User($this->api->request('/users/' . $this->auth->getAuthId())->getArrayBody());
            } else {
                return new User();
            }
        };

        $this['is_guest'] = function () {

            return $this->auth->isGuest();
        };
    }
}