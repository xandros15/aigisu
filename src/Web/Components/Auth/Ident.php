<?php


namespace Aigisu\Web\Components\Auth;


use Aigisu\Components\Api\Api;
use Aigisu\Web\Components\Auth\Ident\Guest;
use Aigisu\Web\Components\Auth\Ident\User;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Pimple\Container;

class Ident extends Container
{
    /** @var Api */
    private $api;
    /** @var JWTAuth */
    private $auth;

    /**
     * Ident constructor.
     *
     * @param Api $api
     * @param AuthInterface $auth
     */
    public function __construct(Api $api, AuthInterface $auth)
    {
        $this->api = $api;
        $this->auth = $auth;
        $this->prepare();
        parent::__construct();
    }

    /**
     * Prepare env
     */
    private function prepare()
    {
        $this['user'] = function (Ident $ident) {
            if (!$ident['is_guest']) {
                try {
                    $user = new User($this->api->request('/users/current')->getArrayBody());
                } catch (ClientException|ServerException $exception) {
                    $user = new Guest();
                }
            } else {
                $user = new Guest();
            }

            return $user;
        };

        $this['is_guest'] = function () {

            return $this->auth->isGuest();
        };
    }
}