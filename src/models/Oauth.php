<?php

namespace models;

use RedBeanPHP\R;
use RedBeanPHP\OODBBean;

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
        return (isset($_SESSION[self::SESSION_NAME]) && $_SESSION[self::SESSION_NAME]);
    }

    public static function load()
    {
        $oauth = new Oauth;
        $oauth->run();
        return $oauth;
    }

    public function run()
    {
        if ($this->startSession() && $this->validatePin()) {
            $this->login();
            header('location: ' . SITE_URL);
        }elseif($this->isLogout()){
            $this->logout();
        }
    }

    public function getErrorLog()
    {
        return $this->errorLog;
    }

    private function startSession()
    {
        return (session_status() == PHP_SESSION_NONE) ? session_start() : false;
    }

    private function isLogout()
    {
        global $query;
        return (!empty($query->post->logout));
    }

    private function logout()
    {
        $_SESSION = [];
        return session_destroy();
    }

    private function login()
    {
        $_SESSION[self::SESSION_NAME] = true;
        $_SESSION['token']            = $this->token;
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
                $this->errorLog[] = 'Pin is outdated';
                return false;
            }
            $this->token = $result->token;
            return true;
        }
        $this->errorLog[] = 'Wrong pin';
        return false;
    }

    private function isTimeout($time)
    {
        return (time() - $time > 0);
    }
}