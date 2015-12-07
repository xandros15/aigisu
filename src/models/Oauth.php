<?php

namespace models;

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;
use app\alert\Alert;

class Oauth
{
    const SESSION_NAME = 'oauth';

    public $errorLog = [];
    private $token;

    public static function tableName()
    {
        return 'oauth';
    }

    public static function isLogged()
    {
        return (isset($_SESSION[self::SESSION_NAME]['run']) && $_SESSION[self::SESSION_NAME]['run']);
    }

    public function run()
    {
        $this->startSession();
        if ($this->validatePin()) {
            $this->login();
            header('location: ' . SITE_URL);
        } elseif ($this->isLogout()) {
            $this->logout();
        }
    }

    public function getErrorLog()
    {
        return $this->errorLog;
    }

    private function startSession()
    {
        return (session_status() == PHP_SESSION_NONE && !session_id()) ? session_start() : false;
    }

    private function isLogout()
    {
        global $query;
        return (!empty($query->post->logout));
    }

    private function logout()
    {
        $_SESSION[self::SESSION_NAME] = [];
        return session_destroy();
    }

    private function login()
    {
        $_SESSION[self::SESSION_NAME]['run']   = true;
        $_SESSION[self::SESSION_NAME]['token'] = $this->token;
    }

    private function validatePin()
    {
        global $query;
        if (empty($query->post->pin) || strlen($query->post->pin) > 32) {
            return false;
        }
        $pin     = $query->post->pin;
        if (($results = R::find(self::tableName(), ' pin = ? ', [$pin]))) {
            /* @var $result OODBBean */
            $result = reset($results);
            if ($this->isTimeout($result->time)) {
                Alert::add('Pin is outdated', Alert::ERROR);
                return false;
            }
            $this->token = $result->token;
            return true;
        }
        Alert::add('Wrong pin', Alert::ERROR);
        return false;
    }

    private function isTimeout($time)
    {
        return (time() - $time > 0);
    }
}